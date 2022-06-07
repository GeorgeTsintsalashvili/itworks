<?php

namespace App\Helpers;

class Paginator{

    private $pages = [];
    private $maxPage = 1;
    private $currentPage = 1;
    private $pad;

    public static function build($numOfItems, $adjacent, $perPage, $curPage, $leftBoundary, $rightBoundary, $pad = '. . .')
    {
       $object = new Paginator();

       $object -> pad = $pad;
       $object -> maxPage = ceil($numOfItems / $perPage);
       $object -> currentPage = $curPage <= $object -> maxPage ? $curPage : $object -> maxPage;

       $curPage = $object -> currentPage;
 	     $rightBoundaryLastNum = $object -> currentPage + $adjacent;
 	     $leftBoundaryLastNum = $object -> currentPage - $adjacent;

 	     if (($leftBoundaryLastNum - $leftBoundary) > 1)
 	     {
 	       for ($i = 1; $i <= $leftBoundary; $i++) $object -> pages[] = ['value' => $i, 'isPad' => false, 'isActive' => $object -> currentPage == $i];

 	       $object -> pages[] = ['value' => $object -> pad, 'isPad' => true, 'isActive' => false];

 	       for ($i = $leftBoundaryLastNum; $i <= $curPage; $i++) $object -> pages[] = ['value' => $i, 'isPad' => false, 'isActive' => $object -> currentPage == $i];
 	     }

 	     if (($leftBoundaryLastNum >= 1 && $leftBoundaryLastNum <= $leftBoundary) || $leftBoundaryLastNum <= 0 || ($leftBoundaryLastNum - $leftBoundary) == 1)
         {
 	       for ($i = 1; $i <= $curPage; $i++) $object -> pages[] =  ['value' => $i, 'isPad' => false, 'isActive' => $object -> currentPage == $i];
         }

 	     if (($rightBoundaryLastNum + 2) <= $object -> maxPage - $rightBoundary)
 	     {
 	       for ($i = $curPage + 1; $i <= $rightBoundaryLastNum; $i++) $object -> pages[] = ['value' => $i, 'isPad' => false, 'isActive' => $object -> currentPage == $i];

 	       $object -> pages[] = ['value' => $object -> pad, 'isPad' => true, 'isActive' => false];

 	       for ($i = $object -> maxPage - $rightBoundary; $i <= $object -> maxPage; $i++) $object -> pages[] = ['value' => $i, 'isPad' => false, 'isActive' => $object -> currentPage == $i];
 	     }

       if (($object -> maxPage - $rightBoundary) - $rightBoundaryLastNum == 1 || $rightBoundaryLastNum >= $object -> maxPage - $rightBoundary)
       {
         for ($i = $curPage + 1; $i <= $object -> maxPage; $i++) $object -> pages[] = ['value' => $i, 'isPad' => false, 'isActive' => $object -> currentPage == $i];
       }

       return $object;
    }

    public function __get($property)
    {
      $value = null;
        
      switch ($property)
      {
        case 'pages':
              $value = $this -> pages;
              break;
              
        case 'currentPage':
              $value = $this -> currentPage;
              break;
              
        case 'maxPage':
              $value = $this -> maxPage;
              break;
      }
      
      return $value;
    }
}
