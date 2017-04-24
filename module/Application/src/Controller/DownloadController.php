<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DownloadController extends AbstractActionController {
    private function getTemp($fname) {
        // function that generates a temporary file path
        // also ensures tmpdir is built from a known system path and exists
        // maximum compatibility!!

        // construct absolute path from known system path
        $tmpdir = join(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), "whinyjerk"]);

        // create if necessary
        if (!is_dir($tmpdir)) mkdir($tmpdir);
        return join(DIRECTORY_SEPARATOR, [$tmpdir, $fname]);
    }

    public function indexAction() {
        return new ViewModel();
    }

    public function generateAction() {
        // create file with random filename and write a fact into it
        $fname = uniqid() . ".txt";
        $fpath = $this->getTemp($fname);
        file_put_contents($fpath, "URE A JERK");

        return new ViewModel(["filename" => $fname]);
    }

    public function downloadAction() {
        $fname = $this->getRequest()->getQuery("filename", "");
        if ($fname == "") exit("THIS IS WRONG");
        $fpath = $this->getTemp($fname);
        if (!file_exists($fpath)) exit("THIS IS ALSO WRONG");

        // send file using ZF3 Response object
        $r = $this->getResponse();

        // add headers for immediate download
        $hdr = $r->getHeaders();
        // octet-stream will ALWAYS force the download dialog
        $hdr->addHeaderLine("Content-type: application/octet-stream");
        $hdr->addHeaderLine("Content-Disposition: attachment; filename=\"" .  $fname . "\"");

        // add actual file content
        $r->setContent(file_get_contents($fpath));

        // you could actually delete the file here probably

        return $r;
    }
}
