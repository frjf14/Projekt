<?php

namespace frjf14\FlashMessage;

class CFlashMessage
{
    public function __construct() {

        if (session_status() == PHP_SESSION_NONE) {

            session_start();
        }
    }

    private function addMessage($message, $type) {


        if(isset($_SESSION['flashmessages'])){

            $flashMessages = $_SESSION['flashmessages'];
        }

        $flashMessage = [
            'message' => $message,
            'type' => $type,
        ];

        $flashMessages[] = $flashMessage;

        $_SESSION['flashmessages'] = $flashMessages;
    }

    public function addError($message) {

        $this->addMessage($message, 'error');
    }

    public function addSuccess($message) {

        $this->addMessage($message, 'success');
    }

    public function addNotice($message) {

        $this->addMessage($message, 'notice');
    }

    public function addWarning($message) {

        $this->addMessage($message, 'warning');
    }

    private function deleteMessages() {

        $_SESSION['flashmessages'] = null;
    }

    public function getFlashMessages() {

        if(isset($_SESSION['flashmessages'])){

            $messages = $_SESSION['flashmessages'];

            $html = "";

            foreach ($messages as $message) {

                $html .= "<div id='flashMessage' class='" . $message['type'] . "'>" . $message['message'] . "</div>";
            }

            $this->deleteMessages();

            return $html;
        }
    }
}
