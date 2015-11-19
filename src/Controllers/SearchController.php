<?php

namespace Canvas\Controllers;

use Paladin\Http\Response;
use Paladin\Paladin;
use Paladin\Std\StringsUtil;

class SearchController
{

    public function display()
    {
        return Paladin::view("search.twig");
    }

    public function search()
    {
        if (!isset($_POST["search"]))
            return Paladin::view("error.twig", array("title" => "Missing argument !", "message" => "We can't find your search content !"));

        $search = $_POST["search"];
        $files = self::listFiles("files");
        $groupResults = array();
        $artifactsResults = array();

        foreach ($files["groups"] as $g)
            if (strpos($g, $search) !== false || strpos(str_replace(".", " ", $g), $search) !== false)
                $groupResults[sizeof($groupResults)] = $g;

        foreach ($files["artifacts"] as $a)
            if (strpos($a, $search) !== false)
                $artifactsResults[sizeof($artifactsResults)] = $a;

        return Paladin::view("search-result.twig", array("groups" => $groupResults, "artifacts" => $artifactsResults));
    }

    public function listFiles($folder, $finalFiles = array("groups" => array(), "artifacts" => array()))
    {
        $files = scandir($folder);
        $files = array_slice($files, 2);

        foreach ($files as $file)
            if (TreeController::is_artifact($folder . "/" . $file))
                $finalFiles["artifacts"][sizeof($finalFiles["artifacts"])] = substr(str_replace("/", ".", $folder) . "/" . $file, 6);
            else
            {
                $finalFiles["groups"][sizeof($finalFiles["groups"])] = substr(str_replace("/", ".", $folder . "/" . $file), 6);
                $finalFiles = self::listFiles($folder . "/" . $file, $finalFiles);
            }

        return $finalFiles;
    }
}