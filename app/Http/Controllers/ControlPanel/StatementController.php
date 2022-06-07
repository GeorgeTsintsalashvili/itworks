<?php

namespace App\Http\Controllers\ControlPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Rules\NaturalNumber;
use App\Rules\BinaryValue;
use App\Rules\Statements;
use App\Rules\StatementsSchedule;
use App\Rules\PositiveIntegerOrZero;

class StatementController extends Controller
{
    public function index(Request $request)
    {
      date_default_timezone_set('Asia/Tbilisi');

      $statementsCurrentPage = 1;
      $statementsCategoryId = 0;

      $parameters = $request -> only([ 'category-id', 'statements-page' ]);

      $rules = [ 'category-id' => [ 'required', new PositiveIntegerOrZero ],
                 'statements-page' => [ 'required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        $statementsCategoryId = (int) $parameters['category-id'];
        $statementsCurrentPage = (int) $parameters['statements-page'];
      }

      $statementsQueryBuilder = \DB::table('statements');
      $categories = \DB::table('statements_categories') -> orderBy('id', 'desc') -> get();
      $statementsData = \DB::table('statements_data') -> first();

      if($statementsCategoryId != 0) $statementsQueryBuilder = $statementsQueryBuilder -> where('categoryId', $statementsCategoryId);

      $numOfStatementsToView = 8;
      $numOfStatements = $statementsQueryBuilder -> count();
      $statementsPaginator = \Paginator::build($numOfStatements, 2, $numOfStatementsToView, $statementsCurrentPage, 2, 2);
      $statementsToSkip = ($statementsPaginator -> currentPage - 1) * $numOfStatementsToView;

      $statements = $statementsQueryBuilder -> orderBy('id', 'desc') -> skip($statementsToSkip) -> take($numOfStatementsToView) -> get();

      $statements -> each(function($statement){

          $statement -> updateTime = date('Y-m-d H:i:s', $statement -> lastUpdateTimestamp);
      });

      return \View::make('contents.controlPanel.statements.index') -> with([

          'categories' => $categories,
          'selectedCategoryId' => $statementsCategoryId,
          'categoriesKey' => 'category-id',
          'statements' => $statements,
          'statementsPageKey' => 'statements-page',
          'statementsPaginator' => $statementsPaginator,
          'statementsData' => $statementsData
      ]);
    }

    // store routes

    public function storeStatement(Request $request)
    {
      $parameters = $request -> only([ 'identifiers', 'updateSchedule', 'categoryId' ]);

      $rules = [ 'identifiers' => [ 'required', new Statements ],
                 'updateSchedule' => [ 'required', 'max:10000', new StatementsSchedule ],
                 'categoryId' => [ 'required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
          $category = \DB::table('statements_categories') -> where('id', $parameters['categoryId']) -> first();

          if(!is_null($category))
          {
            $parameters['updateEnabled'] = $request -> has('updateEnabled') ? 1 : 0;
            $parameters['superVip'] = $request -> has('superVip') ? 1 : 0;

            \DB::table('statements') -> insert($parameters);

            return [ 'success' => true ];
          }
      }

      return [ 'success' => false ];
    }

    public function storeCategory(Request $request)
    {
      $parameters = $request -> only([ 'categoryTitle', 'parameterValue' ]);
      $rules = [ 'categoryTitle' => 'required|string|min:1|max:100',
                 'parameterValue' => 'required|string|min:1|max:100' ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        \DB::table('statements_categories') -> insert($parameters);

        return [ 'success' => true ];
      }

      return [ 'success' => false ];
    }

    // update category

    public function updateStatement(Request $request)
    {
      $parameters = $request -> only([ 'identifiers', 'updateSchedule', 'categoryId', 'record-id', 'updateEnabled', 'superVip' ]);

      $rules = [ 'identifiers' => [ 'required', new Statements ],
                 'updateSchedule' => [ 'required', 'max:10000', new StatementsSchedule ],
                 'categoryId' => [ 'required', new NaturalNumber ],
                 'record-id' => [ 'required', new NaturalNumber ],
                 'updateEnabled' => [ 'required', new BinaryValue ],
                 'superVip' => [ 'required', new BinaryValue ] ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
          $category = \DB::table('statements_categories') -> where('id', $parameters['categoryId']) -> first();

          if(!is_null($category))
          {
            \DB::table('statements') -> where('id', $parameters['record-id']) -> update(\Arr::except($parameters, [ 'record-id' ]));

            return [ 'updated' => true ];
          }
      }

      return [ 'updated' => false ];
    }

    public function updateCategory(Request $request)
    {
      $parameters = $request -> only([ 'categoryTitle', 'parameterValue', 'record-id']);
      $rules = [ 'categoryTitle' => 'required|string|min:1|max:100',
                 'parameterValue' => 'required|string|min:1|max:100',
                 'record-id' => [ 'required', new NaturalNumber ] ];

      $validator = \Validator::make($parameters, $rules);

      if(!$validator -> fails())
      {
        \DB::table('statements_categories') -> where('id', $parameters['record-id']) -> update(\Arr::except($parameters, [ 'record-id' ]));

        return [ 'updated' => true ];
      }

      return [ 'updated' => false ];
    }

    public function updateSessionCookie(Request $request)
    {
       $parameters = $request -> only(['sessionText']);
       $rules = ['sessionText' => 'required|string|min:1|max:4096'];

       $validator = \Validator::make($parameters, $rules);

       if(!$validator -> fails())
       {
         \DB::table('statements_data') -> update($parameters);

         return [ 'success' => true ];
       }

       return [ 'success' => false ];
    }

    // destroy routes

    public function destroyStatement($id)
    {
      try{

          \DB::table('statements') -> where('id', $id) -> delete();

          return ['deleted' => true];
      }

      catch(\Exception $e){

        return ['deleted' => false];
      }
    }

    public function destroyCategory($id)
    {
      try{

          \DB::table('statements_categories') -> where('id', $id) -> delete();

          return ['deleted' => true];
      }

      catch(\Exception $e){

        return ['deleted' => false];
      }
    }
}
