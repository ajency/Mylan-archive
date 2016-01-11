<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;


class SubmissionController extends Controller
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

         return view('hospital.submissions-list')->with('active_menu', 'submission')
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
        $data =  $this->getSubmissionData($responseId);
        $questionnaire = $data['questionnaire'];
        $date = $data['date']; 
        $answersList = $data['answers'];

        $responseQry = new ParseQuery("Response");
        $responseQry->notEqualTo("objectId", $responseId);
        $responseQry->descending("updatedAt");
        $response = $responseQry->first();

        $previousAnswersList =[];
        if(!empty($response))
        {
            $previousData =  $this->getSubmissionData($response->getObjectId());
            $previousAnswersList = $previousData['answers'];
        }

        return view('hospital.submissions-view')->with('active_menu', 'submission')
                                            ->with('questionnaire', $questionnaire)
                                            ->with('date', $date)
                                            ->with('answersList', $answersList)
                                            ->with('previousAnswersList', $previousAnswersList);
    }

    public function getSubmissionData($responseId)
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
           $question =  $answers->get("question");
           $questionId =  $question->getObjectId();
           $questionType =  $question->get("type");


           if($questionType == 'multi-choice')
           {
                if(!isset($answersList[$questionId]))
                {
                   $answersList[$questionId]= [ 'id' => $answers->getObjectId(),
                                        'questionId' => $answers->get("question")->getObjectId(),
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
                                        'questionId' => $answers->get("question")->getObjectId(),
                                        'question' => $answers->get("question")->get("question"), 
                                        'questionType' => $questionType, 
                                        'option' => $option,  
                                        'value' => $answers->get("value"),  
                                        'updatedAt' => $answers->getUpdatedAt()->format('d-m-Y'),    
                          ];
           }
           
        }

        $data = ['questionnaire'=>$questionnaire ,'date'=>$date , 'answers'=>$answersList] ;
        return $data;
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
