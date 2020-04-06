<?php

class NervBot_DataWriter_Response extends XenForo_DataWriter
{
    protected function _getFields()
    {
        $visitor = XenForo_Visitor::getInstance();
        return array(
            'xf_taigachat_response' => array(
                'response_id'       => array('type' => self::TYPE_UINT, 'autoIncrement' => true),
                'detect'            => array('type' => self::TYPE_STRING, 'required' => true),
                'response'          => array('type' => self::TYPE_STRING, 'required' => true),
                'detect_mode'       => array('type' => self::TYPE_STRING, 'required' => true, 'default' => "normal"),
                'response_by'      => array('type' => self::TYPE_UINT, 'required' => true, 'default' => $visitor['user_id']),
            )
        );
    }

    protected function _getExistingData($data)
    {
        if (!$id = $this->_getExistingPrimaryKey($data))
        {
            return false;
        }

        return array('xf_taigachat_response' => $this->_getModel()->getResponseById($id));
    }
    protected function _getUpdateCondition($tableName)
    {
        return 'response_id = ' . $this->_db->quote($this->getExisting('response_id'));
    }
    protected function _getModel()
    {
        return $this->getModelFromCache('NervBot_Model_NervBot');
    }
}

?>
