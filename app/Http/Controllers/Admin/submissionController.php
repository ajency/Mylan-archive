<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;


class submissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $responseQry = new ParseQuery("Response");
        $responseQry->descending("updatedAt");
        $responses = $responseQry->find(); 
        $responseList =[];
         
        foreach($responses as $response)
        {  
 
           $responseList[]= [ 'id' => $response->getObjectId(),
                              'patient' => $response->get("patient"),  
                              'status' => $response->get("status"),  
                              'updatedAt' => $response->getUpdatedAt()->format('d-m-Y'),    
                          ];
        }

         return view('admin.submissions-list')->with('active_menu', 'submission')
                                              ->with('responseList', $responseList);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($responseId)
    {
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("objectId", $responseId);
        $responseQry->includeKey('questionnaire');
        $response = $responseQry->first();

        $answerQry = new ParseQuery("Answer");
        $answerQry->equalTo("response",$response);
        $answerQry->includeKey('question');
        $answerQry->includeKey('option');
        $answers = $answerQry->find(); 
         
        $answersList =[];

        $questionnaire = $response->get("questionnaire")->get("name");
        $date = $response->getUpdatedAt()->format('d-m-Y');
         
        foreach($answers as $answers)
        {  
           $questionId = $answers->get("question")->getObjectId();
           $questionType = $answers->get("question")->get("type");

           if($questionType == 'multi-choice')
           {
                if(!isset($answersList[$questionId]))
                {
                   $answersList[$questionId]= [ 'id' => $answers->getObjectId(),
                                        'question' => $answers->get("question")->get("question"), 
                                        'questionType' => $questionType, 
                                        'option' => [$answers->get("option")->get("label")],  
                                        'value' => $answers->get("value"),  
                                        'updatedAt' => $answers->getUpdatedAt()->format('d-m-Y'),    
                          ]; 
                }
                else
                {

                   $answersList[$questionId]['option'][] = $answers->get("option")->get("label");
                }
                
           }
           else
           {
                $option = ($answers->get("option") !='')?$answers->get("option")->get("label"):'';
                $answersList[$questionId]= [ 'id' => $answers->getObjectId(),
                                        'question' => $answers->get("question")->get("question"), 
                                        'questionType' => $questionType, 
                                        'option' => $option,  
                                        'value' => $answers->get("value"),  
                                        'updatedAt' => $answers->getUpdatedAt()->format('d-m-Y'),    
                          ];
           }
           
        }
         
        return view('admin.submissions-view')->with('active_menu', 'submission')
                                            ->with('questionnaire', $questionnaire)
                                            ->with('date', $date)
                                            ->with('answersList', $answersList);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
