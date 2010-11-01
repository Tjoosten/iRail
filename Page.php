<?php

/**
 * This is the start of all pages. It uses the template design pattern to
 * create the page: it will need a template chosen by the user.
 *
 * @author pieterc
 */
abstract class Page {

    //CONFIG PART
    protected $AVAILABLE_TEMPLATES = array("iRail", "iPhone", "jQueryMobile");
    protected $AVAILABLE_LANGUAGES = array("EN", "NL", "FR", "DE");
    private $globals = array(
        "iRail" => "iRail"
    );
    //DON'T TOUCH
    private $template = "iRail";
    private $lang = "EN";
    //The page content is stored here
    private $content = "";
    private $pageName;
    //This is the array that needs to be filled
    protected $page;
    private $detectLanguageAndTemplate = false;

    public function buildPage($pageName) {
        if ($this->detectLanguageAndTemplate) {
            $this->detectLanguageAndTemplate();
        }
        $this->pageName = $pageName;
        $this->loadTemplate();
        $this->loadContent();
        $this->loadGlobalVariables();
        $this->loadI18n();
        $this->printPage();
    }

    public function setDetectLanguageAndTemplate($bool) {
        $this->detectLanguageAndTemplate = $bool;
    }

    private function detectLanguageAndTemplate() {
        if (isset($_COOKIE["lang"])) {
            $this->setLanguage($_COOKIE["lang"]);
        }
        if (isset($_GET["lang"])) {
            $this->setLanguage($_GET["lang"]);
            setcookie("lang", $_GET["lang"], time() + 60 * 60 * 24 * 360);
        }
        if (isset($_COOKIE["output"])) {
            $this->setTemplate($_COOKIE["output"]);
        }
        if (isset($_GET["output"])) {
            $this->setTemplate($_GET["output"]);
            setcookie("output", $_GET["output"], time() + 60 * 60 * 24 * 360);
        }
    }

    private function setGlobals() {
        $this->globals["GoogleAnalytics"] = file_get_contents("includes/googleAnalytics.php");
        //hack
        if ($this->template == "jQueryMobile") {
            $this->globals["footer"] = '<a href="news.php?output=jQueryMobile">{i18n_news}</a>
<a href="feedback.php?output=jQueryMobile">{i18n_feedback}</a>
<a href="about/?output=jQueryMobile">the iRail team</a>
<a href="http://project.iRail.be" target="_blank">Help us out</a>';
        } else {
            $this->globals["footer"] = file_get_contents("includes/footer.php");
        }
    }

    public function setTemplate($template) {
        if (in_array($template, $this->AVAILABLE_TEMPLATES)) {
            $this->template = $template;
        }
    }

    public function setLanguage($lang) {
        if (in_array($lang, $this->AVAILABLE_LANGUAGES)) {
            $this->lang = $lang;
        }
    }

    private function loadTemplate() {
        $tplPath = "templates/" . $this->template . "/" . $this->pageName;
        if (file_exists($tplPath)) {
            $this->content = file_get_contents($tplPath);
        } else {
            throw new Exception("Template doesn't exist: " . $tplPath);
        }
    }

    private function loadGlobalVariables() {
        $this->setGlobals();
        $this->substituteTagsInContent($this->globals);
    }

    private function loadContent() {
        $this->substituteTagsInContent($this->page);
    }

    private function substituteTagsInContent($tagMap) {
        foreach ($tagMap as $tag => $value) {
            $this->content = str_ireplace("{" . $tag . "}", $value, $this->content);
        }
    }

    private function loadI18n() {
        if ($this->lang == "EN") {
            include_once("i18n/EN.php");
        } else if ($this->lang == "NL") {
            include_once("i18n/NL.php");
        } else if ($this->lang == "FR") {
            include_once("i18n/FR.php");
        } else if ($this->lang == "DE") {
            include_once("i18n/DE.php");
        }

        foreach ($i18n as $tag => $value) {
            $this->content = str_ireplace("{i18n_" . $tag . "}", $value, $this->content);
        }
    }

    private function printPage() {
        echo $this->content;
    }

}
?>
