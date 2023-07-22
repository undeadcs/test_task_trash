<?php
/**
 * @file library/App/Filter/CopyFormData.php
 * ������ ������� ������ �� �������
 * @version $Revision: 17307 $
 * 
 * v0.1
 * 
 * ��� ��������� ������� ���������� �������� ������ ������ �������, ���� ��� � ��������, �� ������������
 * ������������ ���� ���������� �� ������� � �������������� ������� ������
 * 
 * ������ �������� �� ��������:
 * ������ ������ ��������� ���������� ������� � ��� ��������������
 * ���� ������ ������� �������� string (name), �� ��� ������������ array('type' => 'string', 'indexInput' => 'name')
 * ������ ������� ��:
 * type                enum { 'int', 'float', 'string', 'date', 'datetime' }
 * indexInput          string, ��� ������� �� �������� ����� ������
 * indexOutput         string, ������������ ��������� (�� ��������� ����� indexInput), ��� ������� � ������� ����� ������� ���������
 * isOptional          true/false, ������������ ���������, �� ��������� true
 * defaultValue        ������������ ���������, ����� ������� �������� (��� �����������), �� ��������� NULL
 * typeSpecialParams   ������������ ����� ��������, ������� ����� ����������. ��. ����
 * ��� ������� ���� ������ ����� ���� ����������, ������� ���������� �������� �������������� � ��������� �������� ��������
 * �� �����:
 * int:
 *     isUnsigned      true/false, �� ��������� false
 *     minimumValue    int (��� �����������), ���� ������� �������� ������, �� � ��������� ������������ ��� ��������
 *                         �� ���������: �������� ������� ��������
 *     maximumValue    int (��� �����������), ���� ������� �������� ������, �� � ��������� ������������ ��� ��������
 *                         �� ���������: �������� ������� ��������
 * float
 *     minimumValue    float (��� �����������), ���� ������� �������� ������, �� � ��������� ������������ ��� ��������
 *                         �� ���������: �������� ������� ��������
 *     maximumValue    float (��� �����������), ���� ������� �������� ������, �� � ��������� ������������ ��� ��������
 *                         �� ���������: �������� ������� ��������
 * string
 *     minimumLength   int, ����������� ����� ������
 *     maximumLength   int, ������������ ����� ������
 */
class App_Filter_CopyFormData implements Zend_Filter_Interface
{
    /**
     * ��� ������: �������� ����������� �� ������� ������
     */
    const CFD_ERROR_NOTEXISTS = 1;
    
    /**
     * ��� ������: �������� ������ ������������ �������� (��� int, float)
     */
    const CFD_ERROR_ISSMALL = 2;
    
    /**
     * ��� ������: �������� ������ ������������� �������� (��� int, float)
     */
    const CFD_ERROR_ISBIG = 3;
    
    /**
     * ��� ������: �������� �� ����� ������ ����������� (��� string)
     */
    const CFD_ERROR_ISSHORT = 4;
    
    /**
     * ��� ������: �������� �� ����� ������ ������������ (��� string)
     */
    const CFD_ERROR_ISLONG = 5;
    
    /**
     * ��� ������: �������� ������ ����, ����� ��������� ��������� �������� (��� int)
     */
    const CFD_ERROR_ISLESSZERO = 6;
    
    /**
     * ��� ������: �������� �� ������������� ������� (��� date, datetime)
     */
    const CFD_ERROR_INVALIDFORMAT = 7;
    
    /**
     * ����� ������ ��������� ���������
     */
    protected $_errors = array();
    
    /**
     * �������� ������� ������
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }
    
    /**
     * ��������� ������ ������
     */
    public function getErrors()
    {
        return $this->_errors;
    }
    
    /**
     * ������, �� �������� ���������� �����������
     */
    protected $_config = array();
    
    /**
     * ��������� �������
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * ��������� �������
     */
    public function setConfig($config)
    {
        if (is_array($config)) {
            $this->_config = $config;
        }
        
        return $this;
    }
    
    /**
     * �������� �� ��������� �� �����
     */
    protected $_defaultValues = NULL;
    
    /**
     * ��������� �������� �� ��������� �� �����
     */
    public function getDefaultValues()
    {
        return $this->_defaultValues;
    }
    
    /**
     * ��������� �������� �� ��������� �� �����
     */
    public function setDefaultValues($defaultValues)
    {
        if (is_array($defaultValues)) {
            $this->_defaultValues = $defaultValues;
        }
        
        return $this;
    }
    
    public function __construct($config = NULL, $defaultValues = NULL)
    {
        if (is_array($config)) {
            $this->_config = $config;
        }
        
        if (is_array($defaultValues)) {
            $this->_defaultValues = $defaultValues;
        }
    }
    
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Zend_Filter_Exception If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        if (is_array($value)) {
            $ret = array();
            
            foreach($this->_config as $row) {
                if ($this->_isValidConfigRow($row)) {
                    $this->_processInputRow($row, $value, $ret);
                }
            }
            
            return $ret;
        }
        
        return $value;
    }
    
    /**
     * ��������� ������������ ������ �������
     */
    protected function _isValidConfigRow($row)
    {
        return (is_array($row) && isset($row['type']) && isset($row['indexInput'])) || is_string($row);
    }
    
    /**
     * ��������� ������� ������ �� ������� ������ �������
     */
    protected function _processInputRow(&$row, &$input, &$out)
    {
        if (is_string($row)) {
            $tmp = array('type' => 'string', 'indexInput' => $row);
            $this->_string($tmp, $input, $out);
        }
        else {
            switch($row['type']) {
                case 'int':      $this->_int($row, $input, $out);      break;
                case 'float':    $this->_float($row, $input, $out);    break;
                case 'string':   $this->_string($row, $input, $out);   break;
                case 'date':     $this->_date($row, $input, $out);     break;
                case 'datetime': $this->_datetime($row, $input, $out); break;
            }
        }
    }
    
    protected function _int(&$row, &$input, &$out)
    {
        $index = $row['indexInput'];
        $value = NULL;
        
        if (array_key_exists($index, $input) && (is_string($input[$index]) || is_int($input[$index]))) {
            if (is_string($input[$index]) && ($input[$index] == '')) {
                $this->_checkOptional($row, $index);
                $value = $this->_checkDefault($row, 'int');
            }
            else {
                $value = (int)$input[$index];
                $bWasLess = false;
                
                if (isset($row['isUnsigned']) && $row['isUnsigned'] && ($value < 0)) {
                    $value = 0;
                    $bWasLess = true;
                    $this->_errors[] = array($index, self::CFD_ERROR_ISLESSZERO);
                }
                
                if (isset($row['minimumValue']) && is_int($row['minimumValue']) && ($value < $row['minimumValue'])) {
                    $value = $row['minimumValue'];
                    if (!$bWasLess) {
                        $this->_errors[] = array($index, self::CFD_ERROR_ISSMALL);
                    }
                }
                
                if (isset($row['maximumValue']) && is_int($row['maximumValue']) && ($value > $row['maximumValue'])) {
                    $value = $row['maximumValue'];
                    $this->_errors[] = array($index, self::CFD_ERROR_ISBIG);
                }
            }
        }
        else {
            $this->_checkOptional($row, $index);
            $value = $this->_checkDefault($row, 'int');
        }
        
        $this->_setOutputValue($row, $value, $out);
    }
    
    protected function _float(&$row, &$input, &$out)
    {
        $index = $row['indexInput'];
        $value = NULL;
        
        if (array_key_exists($index, $input) && (is_string($input[$index]) || is_float($input[$index]))) {
            if (is_string($input[$index]) && ($input[$index] == '')) {
                $this->_checkOptional($row, $index);
                $value = $this->_checkDefault($row, 'float');
            }
            else {
                $value = (float)$input[$index];
                
                if (isset($row['minimumValue']) && is_float($row['minimumValue']) && ($value < $row['minimumValue'])) {
                    $value = $row['minimumValue'];
                    $this->_errors[] = array($index, self::CFD_ERROR_ISSMALL);
                }
                
                if (isset($row['maximumValue']) && is_float($row['maximumValue']) && ($value > $row['maximumValue'])) {
                    $value = $row['maximumValue'];
                    $this->_errors[] = array($index, self::CFD_ERROR_ISBIG);
                }
            }
        }
        else {
            $this->_checkOptional($row, $index);
            $value = $this->_checkDefault($row, 'float');
        }
        
        $this->_setOutputValue($row, $value, $out);
    }
    
    protected function _string(&$row, &$input, &$out)
    {
        $index = $row['indexInput'];
        $value = NULL;
        
        if (array_key_exists($index, $input) && is_string($input[$index])) {
            if (is_string($input[$index]) && ($input[$index] == '')) {
                $this->_checkOptional($row, $index);
                $value = $this->_checkDefault($row, 'string');
            }
            else {
                $value = (string)$input[$index];
                
                if (isset($row['minimumLength']) && is_int($row['minimumValue']) && (strlen($value) < $row['minimumLength'])) {
                    $this->_errors[] = array($index, self::CFD_ERROR_ISSHORT);
                }
                
                if (isset($row['maximumLength']) && is_int($row['maximumLength']) && (strlen($value) > $row['maximumLength'])) {
                    $value = substr($value, 0, $row['maximumLength']);
                    $this->_errors[] = array($index, self::CFD_ERROR_ISLONG);
                }
            }
        }
        else {
            $this->_checkOptional($row, $index);
            $value = $this->_checkDefault($row, 'string');
        }
        
        $this->_setOutputValue($row, $value, $out);
    }
    
    protected function _date(&$row, &$input, &$out)
    {
        $index = $row['indexInput'];
        $value = NULL;
        
        if (array_key_exists($index, $input) && is_string($input[$index])) {
            if (is_string($input[$index]) && ($input[$index] == '')) {
                $this->_checkOptional($row, $index);
                $value = $this->_checkDefault($row, 'string');
            }
            else {
                $value = (string)$input[$index];
                $bFailed = true;
                
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $bFailed = false;
                }
                
                if (preg_match('/^\d{4}\.\d{2}\.\d{2}$/', $value)) {
                    $value = str_replace('.', '-', $value);
                    $bFailed = false;
                }
                
                if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $value)) {
                    $value = preg_replace('/^(\d{2})\.(\d{2})\.(\d{4})$/', '$3-$2-$1', $value);
                    $bFailed = false;
                }
                
                if ($bFailed) {
                    $this->_errors[] = array($index, self::CFD_ERROR_INVALIDFORMAT);
                }
            }
        }
        else {
            $this->_checkOptional($row, $index);
            $value = $this->_checkDefault($row, 'string');
        }
        
        $this->_setOutputValue($row, $value, $out);
    }
    
    protected function _datetime(&$row, &$input, &$out)
    {
        $index = $row['indexInput'];
        $value = NULL;
        
        if (array_key_exists($index, $input) && is_string($input[$index])) {
            if (is_string($input[$index]) && ($input[$index] == '')) {
                $this->_checkOptional($row, $index);
                $value = $this->_checkDefault($row, 'string');
            }
            else {
                $value = (string)$input[$index];
                $bFailed = true;
                
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                    $bFailed = false;
                }
                
                if (preg_match('/^\d{4}\.\d{2}\.\d{2} \d{2}:\d{2}:\d{2}$/', $value)) {
                    $value = str_replace('.', '-', $value);
                    $bFailed = false;
                }
                
                if (preg_match('/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}:\d{2}$/', $value)) {
                    $value = preg_replace('/^(\d{2})\.(\d{2})\.(\d{4}) (\d{2}:\d{2}:\d{2})$/', '$3-$2-$1 $4', $value);
                    $bFailed = false;
                }
                
                if ($bFailed) {
                    $this->_errors[] = array($index, self::CFD_ERROR_INVALIDFORMAT);
                }
            }
        }
        else {
            $this->_checkOptional($row, $index);
            $value = $this->_checkDefault($row, 'string');
        }
        
        $this->_setOutputValue($row, $value, $out);
    }
    
    protected function _setOutputValue(&$row, &$value, &$out)
    {
        if (isset($row['indexOutput']) && ($row['indexOutput'] != '')) {
            $out[$row['indexOutput']] = $value;
        }
        else {
            $out[$row['indexInput']] = $value;
        }
    }
    
    protected function _checkOptional($row, $index)
    {
        if (isset($row['isOptional']) && is_bool($row['isOptional']) && !$row['isOptional']) {
            $this->_errors[] = array($index, self::CFD_ERROR_NOTEXISTS);
        }
    }
    
    protected function _checkDefault($row, $type)
    {
        if (isset($row['defaultValue']) && call_user_func('is_' . $type, $row['defaultValue'])) {
            return $row['defaultValue'];
        }
        else if (is_array($this->_defaultValues) && array_key_exists($type, $this->_defaultValues)) {
            return $this->_defaultValues[$type];
        }
        
        return NULL;
    }
}