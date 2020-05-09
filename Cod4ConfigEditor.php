<?php

    class Cod4ConfigEditor implements IConfigEditor
    {
        protected $config;
        protected $cfgPath;
        
        public function __construct()
        {
            $this->config = array();
            $this->cfgPath = '';
        }
        
        public function load($cfgPath)
        {
            if (!file_exists($cfgPath))
            {
                throw new Exception('CoD4Editor::load(1): Config does not exist!');
            }
            
            $this->cfgPath = $cfgPath;
            
            $lines = file($cfgPath);
            if ($lines === false)
            {
                throw new Exception('CoD4Editor::load(1): Failed to load the given config!');
            }
            $lineCount = count($lines);
            
            for ($i = 0; $i < $lineCount; $i++)
            {
                $tmp = trim($lines[$i]);
                if (empty($tmp) || substr($tmp, 0, 2) == '//')
                {
                    continue;
                }
                else
                {
                    preg_match('/^(set(?:a|s)?)? (\w+) (.+)$/i', $tmp, $match);
                    $set =& $match[1];
                    $cvar =& $match[2];
                    $value = preg_replace('/^"(.*)"/', '$1', trim($match[3]));
                    $type = ((bool) preg_match('/^\d+$/', $value));
                    
                    $this->config[$cvar] = array(
                        'set' => $set,
                        'cvar' => $cvar,
                        'type' => ($type ? 'number' : 'string'),
                        'value' => $value
                    );
                }
            }
        }
        
        public function save($cfgPath = null)
        {
            if ($cfgPath === null)
            {
                $cfgPath = $this->cfgPath;
                if ($cfgPath === null)
                {
                    throw new Exception('CoD4Editor::save(1): No config path given!');
                }
            }
            
            $dir = dirname($cfgPath);
            
            if (file_exists($cfgPath) && !is_writable($cfgPath))
            {
                throw new Exception('CoD4Editor::save(1): Config file directory is not writable!');
            }
            if (!is_writable($dir))
            {
                throw new Exception('CoD4Editor::save(1): Config directory is not writable!');
            }
            
            $cfgLines = array();
            
            foreach ($this->config as $cvar => &$entry)
            {
                $tmp = $entry['set'] . ' ' . $entry['cvar'] . ' ';
                if ($entry['type'] == 'number')
                {
                    $tmp .= strval(intval($entry['value']));
                }
                else
                {
                    $tmp .= '"' . strval($entry['value']) . '"';
                }
                
                $cfgLines[] = $tmp;
            }
            
            if (file_put_contents($cfgPath, implode("\n", $cfgLines)) === false)
            {
                throw new Exception('CoD4Editor::save(1): Failed to write to config!');
            }
        }
        
        public function cfgPath($cfgPath = null)
        {
            if ($cfgPath === null)
            {
                return $this->configPath;
            }
            elseif (is_string($cfgPath))
            {
                $this->configPath = $cfgPath;
            }
            else
            {
                return false;
            }
        }
        
        
        public function set($name, $value, $type = null, $set = null)
        {
            if ($set != 'set' && $set != 'sets' && $set != 'seta' && $set !== null)
            {
                $set = 'set';
            }
            if ($type != 'string' && $type != 'number' && $type !== null)
            {
                $type = 'string';
            }
            
            if (isset($this->config[$name]))
            {
                $this->config[$name]['value'] = $value;
                if ($type !== null)
                {
                    $this->config[$name]['type'] = $type;
                }
                if ($set !== null)
                {
                    $this->config[$name]['set'] = $set;
                }
            }
            else
            {
                $this->config[$name] = array(
                    'set' => ($set === null ? 'set' : $set),
                    'cvar' => $name,
                    'type' => ($type === null ? 'string' : $type),
                    'value' => $value
                );
            }
        }
        
        public function setArray(array $names, array $values)
        {
            $nameCount = count($names);
            $valueCount = count($values);
            
            if ($nameCount != $valueCount)
            {
                throw new Exception('CoD4Editor::setArray(2): Count of names and values are not equal!');
            }
            
            for ($i = 0; $i < $nameCount; $i++)
            {
                if (!is_array($values))
                {
                    continue;
                }
                $name = (string) $names[$i];
                
                if (isset($values[$i]['set']))
                {
                    $set =& $values[$i]['set'];
                }
                else
                {
                    $set = null;
                }
                
                if (isset($values[$i]['type']))
                {
                    $type =& $values[$i]['type'];
                }
                else
                {
                    $type = null;
                }
                
                if (isset($values[$i]['value']))
                {
                    $value = $values[$i]['value'];
                }
                else
                {
                    $value = '';
                }
                
                $this->set($name, $value, $type, $set);
            }
        }
        
        public function replace(array $names, array $values)
        {
            $this->config = array();
            
            $this->setArray($names, $values);
        }
        
        
        public function get($name)
        {
            if (isset($this->config[$name]))
            {
                return $this->config[$name];
            }
            else
            {
                return null;
            }
        }
        
        public function getArray(array $names)
        {
            $entries = array();
            $nameCount = count($names);
            
            for ($i = 0; $i < $nameCount; $i++)
            {
                $tmp = $this->get($names[$i]);
                if ($tmp !== null)
                {
                    $entries[$names[$i]] = $tmp;
                }
            }
            
            return $entries;
        }
        
        public function getAll()
        {
            return $this->getArray(array_keys($this->config));
        }
    }

?>
