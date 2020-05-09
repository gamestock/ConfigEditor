<?php
    
    class ConfigEditor
    {
        protected static $editorCache = array();
        
        private function __construct()
        {}
        
        public static function &factory($game)
        {
            $class = ucfirst(strtolower($game)) . 'ConfigEditor';
            $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php';
            
            if (isset(self::$editorCache[$path]))
            {
                return self::$editorCache[$path];
            }
            
            if (file_exists($path))
            {
                require_once $path;
                if (class_exists($class)) 
                {
                    $editor = new $class();
                    if ($editor instanceof IConfigEditor)
                    {
                        self::$editorCache[$path] = $editor;
                        
                        return self::$editorCache[$path];
                    }
                    else
                    {
                        throw new Exception('ConfigEditor::factory(1): The given ConfigEditor does not implement IConfigEditor!');
                    }
                }
                else
                {
                    throw new Exception('ConfigEditor::factory(1): The given ConfigEditor is invalid!');
                }
            }
            else
            {
                throw new Exception('ConfigEditor::factory(1): ConfigEditor not found!');
            }
            
        }
    }
    
    interface IConfigEditor
    {
        public function load($cfgPath);
        public function save($cfgPath = null);
        public function cfgPath($cfgPath = null);
        
        public function set($name, $value);
        public function setArray(array $names, array $values);
        public function replace(array $names, array $values);
        
        public function get($name);
        public function getArray(array $names);
        public function getAll();
    }
?>
