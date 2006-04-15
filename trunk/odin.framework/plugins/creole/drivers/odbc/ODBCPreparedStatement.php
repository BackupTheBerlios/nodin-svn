<?php
/*
 *  $Id: ODBCPreparedStatement.php,v 1.1 2004/07/27 23:08:30 hlellelid Exp $
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

require_once 'creole/PreparedStatement.php';
require_once 'creole/common/PreparedStatementCommon.php';
require_once 'creole/util/Lob.php';

/**
 * ODBC specific PreparedStatement functions.
 *
 * @author    Dave Lawson <dlawson@masterytech.com>
 * @version   $Revision: 1.1 $
 * @package   creole.drivers.odbc
 */
class ODBCPreparedStatement extends PreparedStatementCommon implements PreparedStatement
{
    /**
     * This does nothing since ODBC natively supports prepared statements.
     * @see PreparedStatementCommon::replaceParams()
     */
    protected function replaceParams()
    {
        return $this->sql;
    }

    /**
     * Internal function to call native ODBC prepare/execute functions.
     */
    protected function _execute($sql, $params, $fetchmode, $isupdate)
    {
        // Set any params passed directly
        if ($params) {
            for($i=0,$cnt=count($params); $i < $cnt; $i++) {
                $this->set($i+1, $params[$i]);
            }
        }

        // Trim surrounding quotes added from default set methods.
        // Exception: for LOB-based parameters, odbc_execute() will
        // accept a filename surrounded by single-quotes.
        foreach ($this->boundInVars as $idx => $var)
        {
            if ($var instanceof Lob)
            {
                $file = ($isupdate ? $var->getInputFile() : $var->getOutputFile());
                $this->boundInVars[$idx] = "'$file'";
            }
            else if (is_string($var))
            {
                $this->boundInVars[$idx] = trim($var, "\"\'");
            }
        }

        if ($this->resultSet)
        {
            $this->resultSet->close();
            $this->resultSet = null;
        }

        $this->updateCount = null;

        $stmt = @odbc_prepare($this->conn->getResource(), $sql);

        if ($stmt === FALSE)
            throw new SQLException('Could not prepare query', $this->conn->nativeError(), $sql);

        $ret = @odbc_execute($stmt, $this->boundInVars);

        if ($ret === FALSE)
        {
            @odbc_free_result($stmt);
            throw new SQLException('Could not execute query', $this->conn->nativeError(), $sql);
        }

        return $this->conn->createResultSet(new ODBCResultResource($stmt), $fetchmode);
    }

    /**
     * @see PreparedStatement::executeQuery()
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

        $sql = $this->sql;

        if ($this->conn->getAdapter()->hasLimitOffset())
        {
            if ($this->limit > 0 || $this->offset > 0)
                $this->conn->applyLimit($sql, $this->offset, $this->limit);
        }

        $this->resultSet = $this->_execute($sql, $params, $fetchmode, false);

        if (!$this->conn->getAdapter()->hasLimitOffset())
        {
            $this->resultSet->_setOffset($this->offset);
            $this->resultSet->_setLimit($this->limit);
        }

        return $this->resultSet;
    }

    /**
     * @see PreparedStatement::executeUpdate()
     */
    public function executeUpdate($params = null)
    {
        $this->_execute($this->sql, $params, 0, true);
        $this->updateCount = $this->conn->getUpdateCount();

        return $this->updateCount;
    }

    /**
     * @see PreparedStatementCommon::escape()
     */
    protected function escape($str)
    {
        // Nothing to do here. odbc_execute() takes care of escaping strings.
        return $str;
    }

    /**
     * @see PreparedStatement::setNull()
     */
    function setNull($paramIndex)
    {
        $this->boundInVars[$paramIndex] = null;
    }

    /**
     * @see PreparedStatement::setBlob()
     */
    function setBlob($paramIndex, $blob)
    {
        if ($blob === null)
        {
            $this->setNull($paramIndex);
            return;
        }

        if ($blob instanceof Blob)
        {
            if ($blob->isFromFile() && !$blob->isModified())
            {
                $this->boundInVars[$paramIndex] = $blob;
                return;
            }

            $blob = $blob->__toString();
        }

        $this->boundInVars[$paramIndex] = "'" . $this->escape($blob) . "'";
    }

    /**
     * @see PreparedStatement::setClob()
     */
    function setClob($paramIndex, $clob)
    {
        if ($clob === null)
        {
            $this->setNull($paramIndex);
            return;
        }

        if ($clob instanceof Clob)
        {
            if ($clob->isFromFile() && !$clob->isModified())
            {
                $this->boundInVars[$paramIndex] = $clob;
                return;
            }

            $clob = $clob->__toString();
        }

        $this->boundInVars[$paramIndex] = "'" . $this->escape($clob) . "'";
    }

}