<?php

namespace App\Traits;

trait Searchable{

   public static function booleanSearch($queryBuilder, $columns, $searchQuery, $indexedColumns)
   {
     $searchQuery = str_replace(['+', '-', '<', '>', '@', '(', ')', '~', '*', '`', '\'', '"'], '', $searchQuery);
     $searchQuery = trim($searchQuery);
     $searchQuery = preg_replace('/\s{2,}/', ' ', $searchQuery);

     $searchQueryParts = explode(' ', $searchQuery);
     $validCharactersForSearch = [];

     foreach($indexedColumns as $key => $indexedColumn)
     {
       $indexedColumns[$key] = '`' . $indexedColumn . '`';
     }

     foreach($columns as $key => $column)
     {
       $columns[$key] = '`' . $column . '`';
     }

     $fullTextIndex = implode(',', $indexedColumns);
     $columnsToSelect = implode(',', $columns);

     foreach($searchQueryParts as $searchQueryPart)
     {
       $searchQueryPart = trim($searchQueryPart);

       if(strlen($searchQueryPart) != 0)

       $validCharactersForSearch[] = $searchQueryPart .'*';
     }

     $filteredSearchQuery = implode(' ', $validCharactersForSearch);

     $searchCommand = 'MATCH(' . $fullTextIndex . ') AGAINST(? IN BOOLEAN MODE)';
     $columnsToSelect = $columnsToSelect . ',' . $searchCommand . ' AS `relevance`';

     $queryBuilder = $queryBuilder -> selectRaw($columnsToSelect, [$filteredSearchQuery]) -> whereRaw($searchCommand, [$filteredSearchQuery]);

     return $queryBuilder;
   }
}
