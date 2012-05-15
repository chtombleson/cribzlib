<?php
class CribzVeiw {
    private $templatedir;
    private $cachedir = '';
    private $viewdev = false;

    function __construct($templatedir) {
        if (file_exists($templatedir) && is_dir($templatedir)) {
            $this->templatedir = rtrim($templatedir, '/').'/';
        } else {
            throw new CribzViewException("Template Directory: {$templatedir}, does not exist or is not a directory", 1);
        }
    }

    function render($templatefile, $data = array()) {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Template');

        if (file_exists($this->templatedir.$template) && is_file($this->templatedir.$template)) {
            if (!empty($this->cachedir)) {
                $template = new CribzTemplate($this->templatedir.$template, $template, $this->cachedir, $this->viewdev);
            } else {
                $template = new CribzTemplate($this->templatedir.$template, $template, '/tmp/cribzcache/', $this->viewdev);
            }

            $template->output($data);
        } else {
            throw new CribzViewException("Template file does not exist or is not a file", 2);
        }
    }
}
class CribzViewException extends CribzException {}
?>
