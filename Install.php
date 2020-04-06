<?php

class NervBot_Install
{
    public static function install()
	{
        if (!XenForo_Model::create('XenForo_Model_AddOn')->getAddOnById('TaigaChat'))
        {
            throw new XenForo_Exception("La TaigaChat doit être installée pour utiliser NervBot.", true);
            return false;
        }

	    $db = XenForo_Application::get('db');
		$db->query("
			CREATE TABLE if not exists `xf_taigachat_response` (
				`response_id` INT(11) NOT NULL AUTO_INCREMENT,
				`detect` TEXT NOT NULL,
				`response` TEXT NOT NULL,
                `detect_mode` TEXT NOT NULL,
				`response_by` INT(11) NOT NULL,
				PRIMARY KEY (`response_id`)
			)
			COLLATE='utf8_general_ci'
			ENGINE=InnoDB
		");
    }

	public static function uninstall()
	{
	    $db = XenForo_Application::get('db');
        $db->query("DROP TABLE IF EXISTS `xf_taigachat_response`");

	}
}
