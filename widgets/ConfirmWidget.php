<?php


class ConfirmWidget extends HWidget {

    /**
     * @var String Message to show
     */
    public $uniqueID;
    
     /**
     * @var String title to show
     */
    public $title;

    /**
     * @var String Message to show
     */
    public $message;

    /**
     * @var String button name for confirming
     */
    public $buttonTrue = "";

    /**
     * @var String button name for canceling
     */
    public $buttonFalse = "";

    /**
     * @var String content for the displaying link
     */
    public $linkContent;

    /**
     * @var String original path to view
     */
    public $linkHref;


    /**
     * Executes the widget.
     */
    public function run() {
        
        $this->render('confirm', array(
            'uniqueID' => $this->uniqueID,
            'title' => $this->title,
            'message' => $this->message,
            'buttonTrue' => $this->buttonTrue,
            'buttonFalse' => $this->buttonFalse,
            'linkContent' => $this->linkContent,
            'linkHref' => $this->linkHref,
        ));
    }
}

?>