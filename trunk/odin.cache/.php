a:3:{s:7:"actions";a:6:{s:10:"TestAction";a:4:{s:4:"type";s:4:"view";s:5:"roles";a:0:{}s:7:"filters";a:2:{i:0;s:19:"AuthorizationFilter";i:1;s:14:"FallbackFilter";}s:8:"fallback";s:15:"DefaultFallback";}s:15:"DefaultFallback";a:3:{s:4:"type";s:4:"view";s:5:"roles";a:0:{}s:7:"filters";a:2:{i:0;s:19:"AuthorizationFilter";i:1;s:14:"FallbackFilter";}}s:10:"MainAction";a:4:{s:4:"type";s:4:"view";s:5:"roles";a:0:{}s:7:"filters";a:2:{i:0;s:19:"AuthorizationFilter";i:1;s:14:"FallbackFilter";}s:8:"fallback";s:15:"DefaultFallback";}s:11:"LoginAction";a:3:{s:4:"type";s:4:"view";s:5:"roles";a:0:{}s:7:"filters";a:2:{i:0;s:19:"AuthorizationFilter";i:1;s:14:"FallbackFilter";}}s:12:"LogoutAction";a:3:{s:4:"type";s:4:"view";s:5:"roles";a:0:{}s:7:"filters";a:2:{i:0;s:19:"AuthorizationFilter";i:1;s:14:"FallbackFilter";}}s:10:"TreeAction";a:3:{s:4:"type";s:4:"view";s:5:"roles";a:0:{}s:7:"filters";a:2:{i:0;s:19:"AuthorizationFilter";i:1;s:14:"FallbackFilter";}}}s:7:"request";a:4:{s:7:"prace/*";a:3:{s:7:"actions";a:1:{i:0;s:10:"TreeAction";}s:4:"view";a:2:{s:4:"name";s:6:"smarty";s:6:"params";a:1:{s:8:"template";s:8:"tree.tpl";}}s:7:"filters";a:0:{}}s:7:"zaloguj";a:3:{s:7:"actions";a:1:{i:0;s:11:"LoginAction";}s:4:"view";a:2:{s:4:"name";s:6:"smarty";s:6:"params";a:1:{s:8:"template";s:9:"login.tpl";}}s:7:"filters";a:0:{}}s:7:"wyloguj";a:3:{s:7:"actions";a:1:{i:0;s:12:"LogoutAction";}s:4:"view";a:2:{s:4:"name";s:6:"smarty";s:6:"params";a:1:{s:8:"template";s:10:"logout.tpl";}}s:7:"filters";a:0:{}}s:1:"*";a:3:{s:7:"actions";a:1:{i:0;s:10:"MainAction";}s:4:"view";a:2:{s:4:"name";s:6:"smarty";s:6:"params";a:1:{s:8:"template";s:8:"main.tpl";}}s:7:"filters";a:0:{}}}s:7:"plugins";a:7:{s:6:"router";a:1:{s:5:"class";s:6:"Router";}s:12:"actionFabric";a:1:{s:5:"class";s:16:"FileActionFabric";}s:13:"authorization";a:1:{s:5:"class";s:13:"Authorization";}s:14:"authentication";a:1:{s:5:"class";s:14:"Authentication";}s:6:"smarty";a:2:{s:5:"class";s:8:"MySmarty";s:6:"params";a:4:{s:6:"tpldir";s:44:"c:/Dev/Wamp/www/klasa/odin.design/templates/";s:10:"compiledir";s:50:"c:/Dev/Wamp/www/klasa/odin.misc/templates/compile/";s:8:"cachedir";s:48:"c:/Dev/Wamp/www/klasa/odin.misc/templates/cache/";s:9:"configdir";s:37:"c:/Dev/Wamp/www/klasa/odin/odin.misc/";}}s:6:"creole";a:2:{s:5:"class";s:10:"OdinCreole";s:6:"params";a:6:{s:4:"type";s:5:"mysql";s:4:"host";s:9:"localhost";s:8:"username";s:4:"root";s:8:"password";s:0:"";s:8:"database";s:5:"klasa";s:6:"prefix";s:0:"";}}s:5:"plain";a:1:{s:5:"class";s:9:"PlainView";}}}