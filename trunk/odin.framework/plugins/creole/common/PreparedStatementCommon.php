<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */

/**
 * Class that represents a shared code for handling emulated pre-compiled statements.
 * 
 * Many drivers do not take advantage of pre-compiling SQL statements; for these
 * cases the precompilation is emulated.  This emulation comes with slight penalty involved
 * in parsing the queries, but provides other benefits such as a cleaner object model and ability
 * to work with BLOB and CLOB values w/o needing special LOB-specific routines.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 * @version   $Revision: 1.8 $
 * @package   creole.common
 */
abstract class PreparedStatementCommon {

    /**
     * The database connection.
     * @var Connection
     */ 
    protected $conn;
    
    /**
     * Max rows to retrieve from DB.
     * @var int
     */
    protected $limit = 0;
    
    /**
     * Offset at which to start processing DB rows.
     * "Skip X rows"
     * @var int
     */
    protected $offset = 0;
    
    /**
     * The SQL this class operates on.
     * @var string
     */
    protected $sql;

    /**
     * The string positions of the parameters in the SQL.
     * @var array
     */
    protected $positions;


    /**
     * Number of positions (simply to save processing).
     * @var int
     */
    protected $positionsCount;

    /**
     * Map of index => value for bound params.
     * @var array string[]
     */
    protected $boundInVars = array();    
    
    /**
     * Temporarily hold a ResultSet object after an execute() query.
     * @var ResultSet
     */
    protected $resultSet;

    /**
     * Temporary hold the affected row cound after an execute() query.
     * @var int
     */
    protected $updateCount;
    
    /**
     * Create new prepared statement instance.
     * 
     * @param object $conn Connection object
     * @param string $sql The SQL to work with.
     * @param array $positions The positions in SQL of ?'s.
     * @param restult $stmt If the driver supports prepared queries, then $stmt will contain the statement to use.
     */ 
    public function __construct(Connection $conn, $sql)
    {
        $this->conn = $conn;
        $this->sql = $sql;
    
        // get the positiosn for the '?' in the SQL
        // we can move this into its own method if it gets more complex
        $positions = array();
        $length = strlen($sql);
        for ( $position = 0; $position < $length; $position++ ) {
            $c = $sql{$position};
            if ( $c === '?' ) {
                $positions[] = $position;
            } else if ( $c === '"' || $c === "'" ) {
                $position++;
                while ( $position < $length && $sql{$position} !== $c ) {
                    if ( $sql{$position} === '\\' ) $position++;
                    $position++;
                }
            }
        }
    
        $this->positions = $positions;
        $this->positionsCount = count($positions); // save processing later in cases where we may repeatedly exec statement
    }

    /**
     * @see PreparedStatement::setLimit()
     */
    public function setLimit($v)
    {
        $this->limit = (int) $v;
    }
    
    /**
     * @see PreparedStatement::getLimit()
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * @see PreparedStatement::setOffset()
     */ 
    public function setOffset($v)
    {
        $this->offset = (int) $v;
    }
    
    /**
     * @see PreparedStatement::getOffset()
     */
    public function getOffset()
    {
        return $this->offset;
    }
    
    /**
     * @see PreparedStatement::getResultSet()
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * @see PreparedStatement::getUpdateCount()
     */
    public function getUpdateCount()
    {
        return $this->updateCount;
    }
    
    /**
     * @see PreparedStatement::getMoreResults()
     */
    public function getMoreResults()
    {
        if ($this->resultSet) $this->resultSet->close();
        $this->resultSet = null;
        return false;
    }
     
    /**
     * @see PreparedStatement::getConnection()
     */
    public function getConnection()
    {
        return $this->conn;
    }
    
    /**
     * Statement resources do not exist for emulated prepared statements,
     * so this just returns <code>null</code>.
     * @return null
     */
    public function getResource()
    {
        return null;
    }
    
    /**
     * Nothing to close for emulated prepared statements.
     */
    public function close()
    {       
    }
    
    /**
     * Replaces placeholders with the specified parameter values in the SQL.
     * 
     * This is for emulated prepared statements.
     * 
     * @return string New SQL statement with parameters replaced.
     * @throws SQLException - if param not bound.
     */
    protected function replaceParams()
    {
        // Default behavior for this function is to behave in 'emulated' mode.    
        $sql = '';    
        $last_position = 0;

        for ($position = 0; $position < $this->positionsCount; $position++) {
            if (!isset($this->boundInVars[$position + 1])) {
                throw new SQLException('Replace params: undefined query param: ' . ($position + 1));
            }
            $current_position = $this->positions[$position];            
            $sql .= substr($this->sql, $last_position, $current_position - $last_position);
            $sql .= $this->boundInVars[$position + 1];                    
            $last_position = $current_position + 1;            
        }
        // append the rest of the query
        $sql .= substr($this->sql, $last_position);
        
        return $sql;
    }

    /**
     * Executes the SQL query in this PreparedStatement object and returns the resultset generated by the query.
     * We support two signatures for this method:
     * - $stmt->executeQuery(ResultSet::FETCHMODE_NUM);
     * - $stmt->executeQuery(array($param1, $param2), ResultSet::FETCHMODE_NUM);
     * @param mixed $p1 Either (array) Parameters that will be set using PreparedStatement::set() before query is executed or (int) fetchmode.
     * @param int $fetchmode The mode to use when fetching the results (e.g. ResultSet::FETCHMODE_NUM, ResultSet::FETCHMODE_ASSOC).
     * @return ResultSet
     * @throws SQLException if a database access error occurs.
     */
    public function executeQuery($p1 = null, $fetchmode = null)
    {    
        $params = null;
        if ($fetchmode !== null) {
            $params = $p1;
        } elseif ($p1 !== null) {
            if (is_array($p1)) $params = $p1;
            else $fetchmode = $p1;
        }
        
        if ($params) {
            for($i=0,$cnt=count($params); $i < $cnt; $i++) {
                $this->set($i+1, $params[$i]);
            }
        }
        
        $this->updateCount = null; // reset
        $sql = $this->replaceParams();        
        
        if ($this->limit > 0 || $this->offset > 0) {
            $this->conn->applyLimit($sql, $this->offset, $this->limit);
        }
        
        $this->resultSet = $this->conn->executeQuery($sql, $fetchmode);
        return $this->resultSet;
    }

    /**
     * Executes the SQL INSERT, UPDATE, or DELETE statement in this PreparedStatement object.
     * 
     * @param array $params Parameters that will be set using PreparedStatement::set() before query is executed.
     * @return int Number of affected rows (or 0 for drivers that return nothing).
     * @throws SQLException if a database access error occurs.
     */
    public function executeUpdate($params = null) 
    {
        if ($params) {
            for($i=0,$cnt=count($params); $i < $cnt; $i++) {
                $this->set($i+1, $params[$i]);
            }
        }

        if($this->resultSet) $this->resultSet->close();
        $this->resultSet = null; // reset                
        $sql = $this->replaceParams();        
        $this->updateCount = $this->conn->executeUpdate($sql);
        return $this->updateCount;
    }    

    /**
     * Escapes special characters (usu. quotes) using native driver function.
     * @param string $str The input string.
     * @return string The escaped string.
     */
    abstract protected function escape($str);
    
    /**
     * A generic set method.
     * 
     * You can use this if you don't want to concern yourself with the details.  It involves
     * slightly more overhead than the specific settesr, since it grabs the PHP type to determine
     * which method makes most sense.
     * 
     * @param int $paramIndex
     * @param mixed $value
     * @return void
     * @throws SQLException
     */
    function set($paramIndex, $value)
    {
        $type = gettype($value);
        if ($type == "object") {
            if (is_a($value, 'Blob')) {
                $this->setBlob($paramIndex, $value);
            } elseif (is_a($value, 'Clob')) {
                $this->setClob($paramIndex, $value);
            } elseif (is_a($value, 'Date')) {
                 // can't be sure if the column type is a DATE, TIME, or TIMESTAMP column
                 // we'll just use TIMESTAMP by default; hopefully DB won't complain (if
                 // it does, then this method just shouldn't be used).
                 $this->setTimestamp($paramIndex, $value);
            } else {
                throw new SQLException("Unsupported object type passed to set(): " . get_class($value));
            }
        } else {
            if ($type == "integer") {
                $type = "int";
            } elseif ($type == "double") {
                $type = "float";
            }
            $setter = 'set' . ucfirst($type); // PHP types are case-insensitive, but we'll do this in case that changes
            $this->$setter($paramIndex, $value);
        }        
    }
    
    /**
     * Sets an array.
     * Unless a driver-specific method is used, this means simply serializing
     * the passed parameter and storing it as a string.
     * @param int $paramIndex
     * @param array $value
     * @return void
     */
    function setArray($paramIndex, $value) 
    {        
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = "'" . $this->escape(serialize($value)) . "'";
        }
    }

    /**
     * Sets a boolean value.
     * Default behavior is true = 1, false = 0.
     * @param int $paramIndex
     * @param boolean $value
     * @return void
     */
    function setBoolean($paramIndex, $value) 
    {                
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = (int) $value;
        }
    }
    

    /**
     * @see PreparedStatement::setBlob()
     */
    function setBlob($paramIndex, $blob) 
    {        
        if ($blob === null) {
            $this->setNull($paramIndex);
        } else {
            // they took magic __toString() out of PHP5.0.0; this sucks
            if (is_object($blob)) {
                $blob = $blob->__toString();
            }            
            $this->boundInVars[$paramIndex] = "'" . $this->escape($blob) . "'";
        }
    } 

    /**
     * @see PreparedStatement::setClob()
     */
    function setClob($paramIndex, $clob) 
    {
        if ($clob === null) {
            $this->setNull($paramIndex);
        } else {      
            // they took magic __toString() out of PHP5.0.0; this sucks
            if (is_object($clob)) {
                $clob = $clob->__toString();
            }              
            $this->boundInVars[$paramIndex] = "'" . $this->escape($clob) . "'";
        }
    }     

    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    function setDate($paramIndex, $value) 
    {
        if (is_numeric($value)) $value = date("Y-m-d", $value);
        if (is_object($value)) $value = date("Y-m-d", $value->getTime());        
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = "'" . $this->escape($value) . "'";
        }
    } 
    
    /**
     * @param int $paramIndex
     * @param double $value
     * @return void
     */
    function setDecimal($paramIndex, $value) 
    {
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = (float) $value;
        }
    }             

    /**
     * @param int $paramIndex
     * @param double $value
     * @return void
     */
    function setDouble($paramIndex, $value) 
    {
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = (double) $value;
        }
    } 
        
    /**
     * @param int $paramIndex
     * @param float $value
     * @return void
     */
    function setFloat($paramIndex, $value) 
    {
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = (float) $value;
        }
    } 

    /**
     * @param int $paramIndex
     * @param int $value
     * @return void
     */
    function setInt($paramIndex, $value) 
    {
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = (int) $value;
        }
    } 
    
    /**
     * Alias for setInt()
     * @param int $paramIndex
     * @param int $value
     */
    function setInteger($paramIndex, $value)
    {
        $this->setInt($paramIndex, $value);
    }

    /**
     * @param int $paramIndex
     * @return void
     */
    function setNull($paramIndex) 
    {
        $this->boundInVars[$paramIndex] = 'NULL';
    }

    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    function setString($paramIndex, $value) 
    {
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            // it's ok to have a fatal error here, IMO, if object doesn't have
            // __toString() and is being passed to this method.
            $value = (is_object($value) ? $value->__toString() : (string) $value);
            $this->boundInVars[$paramIndex] = "'" . $this->escape($value) . "'";
        }
    } 
    
    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    function setTime($paramIndex, $value) 
    {        
        if (is_numeric($value)) $value = date("H:i:s", $value);
        elseif (is_object($value)) $value = date("H:i:s", $value->getTime());
        
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = "'" . $this->escape($value) . "'";
        }
    }
    
    /**
     * @param int $paramIndex
     * @param string $value
     * @return void
     */
    function setTimestamp($paramIndex, $value) 
    {
        if (is_numeric($value)) $value = date('Y-m-d H:i:s', $value);
        elseif (is_object($value)) $value = date("Y-m-d H:i:s", $value->getTime());
        
        if ($value === null) {
            $this->setNull($paramIndex);
        } else {
            $this->boundInVars[$paramIndex] = "'".$value."'";
        }
    }
            
}
