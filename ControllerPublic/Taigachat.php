<?php

/**
* NervBot
* Développé par Maxime LE HENAFF
* 2016
**/

class NervBot_ControllerPublic_Taigachat extends XFCP_NervBot_ControllerPublic_Taigachat
{
    /**
    * Confction du controler Taigachat étendue afin d'y faire passer deux paramètres supplémentaires
    **/
    public function actionIndex(){
        $parent = parent::actionIndex();
        $route  = XenForo_Application::get('options')->get('dark_taigachat_route') . '/responses';

        $parent->params['canManageResponse'] = $this->_getResponsesModel()->canManageResponse();
        $parent->params['taigachatRoute']    = $route;
        return $parent;
    }

    /**
    * Fonction du controler Taigachat étendue afin de chercher une occurence avec le message posté
    **/
    public function actionPost(){
        $parent  = parent::actionPost();
        $message = $this->_input->filterSingle('message', XenForo_Input::STRING);
        $options = XenForo_Application::get('options');

        $occurence = $this->_getResponsesModel()->getOccurence($message);

        $bot = $this->_getUserModel()->getUserById($options->nervbot_bot_id);
        if(!$bot){
            $bot['user_id']  = 0;
            $bot['username'] = "BOT";
        }

        if($occurence !== false){
            $dw = XenForo_DataWriter::create('Dark_TaigaChat_DataWriter_Message');
            $dw->set('user_id',  $bot['user_id']);
            $dw->set('username', $bot['username']);
            $dw->set('message',  $occurence['response']);
            $dw->save();
        }

        return $parent;
    }

    /**
    * Fonction Responses ajoutée au controler Taigachat
    * Ajoute une réponse
    **/
    public function actionResponses(){

        if(!$this->_getResponsesModel()->canManageResponse()){
            throw getErrorOrNoPermissionResponseException('');
        }

        $route     = XenForo_Application::get('options')->get('dark_taigachat_route');
        $perPage   =  XenForo_Application::get('options')->get('nervbot_response_perpage');
        $page      = $this->_input->filterSingle('page', XenForo_Input::UINT);
        $total     = $this->_getResponsesModel()->countAllResponses();

        $responses = $this->_getResponsesModel()->getAllResponses(array(
            'perPage' => $perPage,
            'page'    => $page
        ));

        if($this->getRequest()->isPost()){
            $input = $this->_input->filter(array(
                'detect'   => XenForo_Input::STRING,
                'response' => XenForo_Input::STRING,
                'mode'     => XenForo_Input::STRING,
            ));

            if(empty($input['detect']) || empty($input['response']) || !in_array($input['mode'], ['normal', 'strict'])){
                return $this->responseError(new XenForo_Phrase('nervbot_invalid_fields'));
            }

            $dw = XenForo_DataWriter::create('NervBot_DataWriter_Response');
            $dw->set('detect',      $input['detect']);
            $dw->set('response',    $input['response']);
            $dw->set('response',    $input['response']);
            $dw->set('detect_mode', $input['mode']);
            $dw->save();

            return $this->responseRedirect(
                XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink($route . '/responses')
            );
        }

        return $this->responseView('', 'nervbot_add', array(
            'responses'   => $responses,
            'page'        => $page,
            'perPage'     => $perPage,
            'total'       => $total,
            'route'       => $route . '/responses',
            'deleteRoute' => $route . '/delete-response'
        ));
    }

    /**
    * Supprime une réponse
    **/
    public function actionDeleteResponse(){
        if(!$this->_getResponsesModel()->canManageResponse()){
            throw getErrorOrNoPermissionResponseException('');
        }

        $routes   = XenForo_Application::get('options')->get('dark_taigachat_route') . '/responses';
        $id       = $this->_input->filterSingle('id', XenForo_Input::UINT);
        $response = $this->_getResponsesModel()->getResponseById($id);

        if(!$response){
            return $this->responseError(new XenForo_Phrase('nervbot_response_not_found'), 404);
        }

        $dw = XenForo_DataWriter::create('NervBot_DataWriter_Response');
        $dw->setExistingData($id);
        $dw->delete();

        return $this->responseRedirect(
            XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink($routes)
        );
    }

    /**
    * @return XenForo_Model_User
    **/
    protected function _getUserModel(){
        return $this->getModelFromCache('XenForo_Model_User');
    }

    /**
    * @return NervBot_Model_NervBot
    **/
    protected function _getResponsesModel(){
        return $this->getModelFromCache('NervBot_Model_NervBot');
    }

}

?>
