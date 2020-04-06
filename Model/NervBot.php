<?php

class NervBot_Model_NervBot extends XenForo_Model {

    /**
    * Retourne une entrée spéficique de la table xf_taigachat_response
    * @param Int id
    * @return array
    **/
    public function getResponseById($id){
        return $this->_getDb()->fetchRow('SELECT * FROM xf_taigachat_response WHERE response_id = ?', $id);
    }

    /**
    * Retourne les entrées de la table xf_taigachat_response
    * @param array Fetch options
    * @return array
    **/
    public function getAllResponses(array $fetchOptions = array()){
        $limitOptions = $this->prepareLimitFetchOptions($fetchOptions);

        $responses = $this->_getDb()->fetchAll($this->limitQueryResults('
            SELECT * FROM xf_taigachat_response ORDER BY response_id DESC
            ', $limitOptions['limit'], $limitOptions['offset']
        ));

        foreach($responses as &$response){
            $response['response_by'] = $this->_getUserModel()->getUserById($response['response_by']);
            $response['detect_mode'] = ucfirst($response['detect_mode']);
        }

        return $responses;
    }

    /**
    * Compte le nombre d'entrée dans la table xf_taigachat_response
    * @return int
    **/
    public function countAllResponses(){
        return count($this->_getDb()->fetchAll('SELECT * FROM xf_taigachat_response'));
    }

    /**
    * Regarde s'il y a une occurence entre le message posté et la détection d'une réponse pré-définie
    * @param String Message
    * @return Array or False
    **/
    public function getOccurence($string){
        foreach($this->getAllResponses() as $response){
            if($response['detect_mode'] == "Strict")
            {
                if(strpos($string, $response['detect']) !== false){
                    return $response;
                }
            }

            if($response['detect_mode'] == 'Normal')
            {
                if($response['detect'] == trim($string))
                {
                    return $response;
                }
            }
        }

        return false;
    }

    /**
    * Vérifie si l'utilisateur a les permissions pour gérer les réponses pré-définies
    * @return true or false
    **/
    public function canManageResponse(array $viewingUser = null){

        if(empty($viewingUser))
        {
            $this->standardizeViewingUserReference($viewingUser);
        }

        if (!$viewingUser['user_id'] || !XenForo_Permission::hasPermission($viewingUser['permissions'], 'nervbot', 'manage'))
        {
            return false;
        }

        return true;
    }

    /**
    * @return XenForo_Model_User
    **/
    protected function _getUserModel(){
        return XenForo_Model::create('XenForo_Model_User');
    }

}

?>
