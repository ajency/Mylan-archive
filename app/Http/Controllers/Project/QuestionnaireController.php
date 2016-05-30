<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;
use App\Hospital;
use App\Projects;
use \Session;
use App\Http\Controllers\Project\ProjectController;
use \Input;
use \Log;


class QuestionnaireController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
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
	public function show($id)
	{
		//
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

	public function getFirstQuestion($questions)
	{
		$questionId ='';
		foreach ($questions as   $question) {
			if(is_null($question->get('previousQuestion')) && $question->get('isChild')==false)
			{
				$questionId = $question->getObjectId();
				break;
			}
				
			
		}

		return $questionId;
	}

	public function getSequenceQuestions($questions,$subQuestionsFlag=false)
	{

		$questionsList = [];
		$sequenceQuestions = [];
		$subQuestions = [];
		foreach ($questions as   $question) {
			$questionId = $question->getObjectId();
			$nextQuestionId = (!is_null($question->get('nextQuestion')))? $question->get('nextQuestion')->getObjectId():'';
			$previousQuestionId = (!is_null($question->get('previousQuestion')))? $question->get('previousQuestion')->getObjectId():'';
			
			$questionType = $question->get('type');
			$title = $question->get('title');
			$name = $question->get('question');
			$isChild = $question->get('isChild');
			if(!$isChild)
				$questionsList[$questionId] = ['nextQuestionId'=>$nextQuestionId,'question'=>$name,'title'=>$title,'type'=>$questionType];
			elseif($subQuestionsFlag)
				$subQuestions[$previousQuestionId][$questionId] = ['previousQuestionId'=>$previousQuestionId,'question'=>$name,'title'=>$title,'type'=>$questionType];


		}

		
		$firstQuestionId = $this->getFirstQuestion($questions);

		$orderQuestions = (!empty($questionsList))? $this->orderQuestions($questionsList,$firstQuestionId,[]) :[];

		if(!empty($subQuestions))
		{
			$orderQuestions = $this->addSubQuestionToList($orderQuestions,$subQuestions);
		}
		
		return $orderQuestions;
	}

	public function orderQuestions($questionsList,$firstQuestionId,$questions)
	{
		$questions[$firstQuestionId] = $questionsList[$firstQuestionId];
		$nextQuestionId = $questionsList[$firstQuestionId]['nextQuestionId'];

		if(count($questions)!=count($questionsList))
			$questions = $this->orderQuestions($questionsList,$nextQuestionId,$questions);
		 
		return $questions;

	}

	public function addSubQuestionToList($orderQuestions,$subQuestions)
	{
		$newOrder = [];
		foreach ($orderQuestions as $questionId => $questionData) {

			$newOrder[$questionId] = $questionData; 
			if(isset($subQuestions[$questionId]))
			{
				$newOrder = array_merge($newOrder,$subQuestions[$questionId]); 
			}
		}

		return $newOrder;
	}


	public function questionnaireSetting($hospitalSlug,$projectSlug)
	{
		try
		{
		  $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

		  $hospital = $hospitalProjectData['hospital'];
		  $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

		  $project = $hospitalProjectData['project'];
		  $projectId = intval($project['id']);

		  $questionnaireQry = new ParseQuery("Questionnaire");
		  $questionnaireQry->equalTo("project",$projectId);
		  $questionnaire = $questionnaireQry->first();

		  $questionnaireId ="";
		  $settings =[];
		  $settings['frequency']['day'] = ''; 
		  $settings['frequency']['hours'] = ''; 
		  $settings['gracePeriod']['day'] = '';
		  $settings['gracePeriod']['hours'] = '';
		  $settings['reminderTime']['day'] = '';
		  $settings['reminderTime']['hours'] = '';
		  $settings['editable'] = '';
		  $settings['type'] = ''; 
		  $settings['name'] = ''; 
		  $settings['pauseProject'] = '';  
		  $settings['status'] = '';  
		  
		  $action ="store-questionnaire-setting";
		  if(!empty($questionnaire))
		  {
			$gracePeriod = secondsToTime($questionnaire->get('gracePeriod'));
			$settings['gracePeriod']['day'] = $gracePeriod['d']; 
			$settings['gracePeriod']['hours'] = $gracePeriod['h']; 

			$reminderTime = secondsToTime($questionnaire->get('reminderTime'));
			$settings['reminderTime']['day'] = $reminderTime['d']; 
			$settings['reminderTime']['hours'] = $reminderTime['h']; 

			$settings['editable'] = $questionnaire->get('editable');
			$settings['type'] = $questionnaire->get('type');
			$settings['name'] = $questionnaire->get('name');
			$settings['pauseProject'] = ($questionnaire->get('pauseProject')==true)?'yes':'no';
			$settings['status'] = $questionnaire->get('status');

			$scheduleQry = new ParseQuery("Schedule");
			$scheduleQry->equalTo("questionnaire",$questionnaire);
			$scheduleQry->doesNotExist("patient");
			$schedule = $scheduleQry->first();
			
			if(!empty($schedule))
			{
	 
			  $frequency = secondsToTime($schedule->get('frequency'));
			  $settings['frequency']['day'] = $frequency['d']; 
			  $settings['frequency']['hours'] = $frequency['h']; 
			}
			$action ="update-questionnaire-setting";
			$questionnaireId = $questionnaire->getObjectId();

		  }
	   
		
		} catch (\Exception $e) {
			Log::error($e->getMessage());
			abort(404);   
		}      


		return view('project.questionnaire-setting')->with('active_menu', 'settings')
										->with('hospital', $hospital)
										->with('project', $project)
										->with('action', $action)
										->with('questionnaireId', $questionnaireId)
										->with('settings', $settings);
	}

	public function storeQuestionnaireSetting(Request $request,$hospitalSlug,$projectSlug)
	{

		try
		{
			$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

			$hospital = $hospitalProjectData['hospital'];
			$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

			$project = $hospitalProjectData['project'];
			$projectId = intval($project['id']);

			$frequencyDay = $request->input('frequencyDay');   
			$frequencyHours = $request->input('frequencyHours');  
			$gracePeriodDay = $request->input('gracePeriodDay');
			$gracePeriodHours = $request->input('gracePeriodHours');
			$reminderTimeDay = $request->input('reminderTimeDay');
			$reminderTimeHours = $request->input('reminderTimeHours');
			$name = $request->input('name');   

			$frequency = strval(convertToSeconds($frequencyDay,$frequencyHours));   
			$gracePeriod = intval(convertToSeconds($gracePeriodDay,$gracePeriodHours));   
			$reminderTime = intval(convertToSeconds($reminderTimeDay,$reminderTimeHours));   

			$editable = ($request->input('editable')=='yes')?true:false;
			$pauseProject = ($request->input('pauseProject')=='yes')?true:false;
			$type = $request->input('type');

			$project->project_status = ($request->input('pauseProject')=='yes')?"paused":"active";
			$project->save();

			$questionnaire = new ParseObject("Questionnaire");
			$questionnaire->set("project",$projectId);
			$questionnaire->set("name",$name);
			$questionnaire->set('gracePeriod',$gracePeriod);
			$questionnaire->set('reminderTime',$reminderTime);
			$questionnaire->set('editable',$editable);
			$questionnaire->set('pauseProject',$pauseProject);
			$questionnaire->set('type',$type);
			$questionnaire->set('status',"draft");
			$questionnaire->save();

	 
			$schedule = new ParseObject("Schedule");
			$schedule->set("questionnaire", $questionnaire);
			$schedule->set("frequency", $frequency);
			$schedule->save();

			Session::flash('success_message','Project settings successfully created.');
			
		} catch (\Exception $e) {

			Log::error($e->getMessage());
			abort(404);         
		}
		return redirect(url($hospitalSlug .'/'. $projectSlug .'/questionnaire-setting')); 
	}

	public function saveQuestionnaireSetting(Request $request,$hospitalSlug,$projectSlug)
	{

		try
		{
			$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

			$hospital = $hospitalProjectData['hospital'];
			$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

			$project = $hospitalProjectData['project'];
			$projectId = intval($project['id']);

			$frequencyDay = $request->input('frequencyDay');   
			$frequencyHours = $request->input('frequencyHours');  
			$gracePeriodDay = $request->input('gracePeriodDay');
			$gracePeriodHours = $request->input('gracePeriodHours');
			$reminderTimeDay = $request->input('reminderTimeDay');
			$reminderTimeHours = $request->input('reminderTimeHours');
			$name = $request->input('name');   

			$frequency = strval(convertToSeconds($frequencyDay,$frequencyHours));   
			$gracePeriod = intval(convertToSeconds($gracePeriodDay,$gracePeriodHours));   
			$reminderTime = intval(convertToSeconds($reminderTimeDay,$reminderTimeHours));   

			$editable = ($request->input('editable')=='yes')?true:false;
			$pauseProject = ($request->input('pauseProject')=='yes')?true:false;
			$type = $request->input('type');

			$project->project_status = ($request->input('pauseProject')=='yes')?"paused":"active";
			$project->save();

			$questionnaireQry = new ParseQuery("Questionnaire");
			$questionnaireQry->equalTo("project",$projectId);
			$questionnaire = $questionnaireQry->first();

			$questionnaire->set("name",$name);
			$questionnaire->set('gracePeriod',$gracePeriod);
			$questionnaire->set('reminderTime',$reminderTime);
			$questionnaire->set('editable',$editable);
			$questionnaire->set('pauseProject',$pauseProject);
			$questionnaire->set('type',$type);
			$questionnaire->save();


			$scheduleQry = new ParseQuery("Schedule");
			$scheduleQry->equalTo("questionnaire",$questionnaire);
			$scheduleQry->doesNotExist("patient");
			$schedule = $scheduleQry->first();

			if(empty($schedule))
			{
			  $schedule = new ParseObject("Schedule");
			  $schedule->set("questionnaire", $questionnaire);
			}

			$schedule->set("frequency", $frequency);
			$schedule->save();

			Session::flash('success_message','Project settings successfully updated.');
			
		} catch (\Exception $e) {

			Log::error($e->getMessage());
			abort(404);         
		}
		return redirect(url($hospitalSlug .'/'. $projectSlug .'/questionnaire-setting')); 
	}

	public function getQuestionsSummary($hospitalSlug,$projectSlug,$questionnaireId)
	{
		try
		{
			$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

			$hospital = $hospitalProjectData['hospital'];
			$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

			$project = $hospitalProjectData['project'];
			$projectId = intval($project['id']);

			$questionnaireObj = new ParseQuery("Questionnaire");
			$questionnaire = $questionnaireObj->get($questionnaireId);

			$questionObjs = new ParseQuery("Questions");
			$questionObjs->equalTo("questionnaire",$questionnaire);
			$questionObjs->ascending("createdAt");
			$questions = $questionObjs->find();

			$questionsList = $this->getSequenceQuestions($questions,true);

			$optionObjs = new ParseQuery("Options");
			$optionObjs->containedIn("question",$questions);
			$optionObjs->ascending("score");
			$options = $optionObjs->find();

			$optionsList = [];
			foreach ($options as $option) {
				$label = $option->get("label");
				$score = $option->get("score");
				$optionId = $option->getObjectId();
				$questionId = $option->get("question")->getObjectId();

				$optionsList[$questionId][] = ['optionId'=>$optionId, 'score'=>$score, 'label'=>$label];         
			}

		   
		
		} catch (\Exception $e) {
			Log::error($e->getMessage());
			abort(404);   
		}      

		

		return view('project.questions-summary')->with('active_menu', 'settings')
										->with('questionnaireId', $questionnaireId)
										->with('hospital', $hospital)
										->with('project', $project)
										->with('optionsList', $optionsList)
										->with('questionsList', $questionsList);
	}

	public function configureQuestions($hospitalSlug,$projectSlug,$questionnaireId)
	{
		try
		{
			$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

			$hospital = $hospitalProjectData['hospital'];
			$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

			$project = $hospitalProjectData['project'];
			$projectId = intval($project['id']);

			$questionnaireObj = new ParseQuery("Questionnaire");
			$questionnaire = $questionnaireObj->get($questionnaireId);

			$questionObjs = new ParseQuery("Questions");
			$questionObjs->equalTo("questionnaire",$questionnaire);
			$questionObjs->ascending("createdAt");
			$questions = $questionObjs->find();

			// $questionsList = [];
			// foreach ($questions as $question) {
			//   $questionTxt = $question->get("question");
			//   $title = $question->get("title");
			//   $type = $question->get("type");
			//   $questionId = $question->getObjectId();

			//   $questionsList[$questionId] = ['question'=>$questionTxt, 'title'=>$title, 'type'=>$type];         
			// }
			$questionsList = $this->getSequenceQuestions($questions,true);

			$optionObjs = new ParseQuery("Options");
			$optionObjs->containedIn("question",$questions);
			$optionObjs->ascending("score");
			$options = $optionObjs->find();

			$optionsList = [];
			foreach ($options as $option) {
				$label = $option->get("label");
				$score = $option->get("score");
				$optionId = $option->getObjectId();
				$questionId = $option->get("question")->getObjectId();

				$optionsList[$questionId][] = ['optionId'=>$optionId, 'score'=>$score, 'label'=>$label];         
			}

		   
		
		} catch (\Exception $e) {
			Log::error($e->getMessage());
			abort(404);   
		}      

		

		return view('project.configure-questions')->with('active_menu', 'settings')
										->with('questionnaireId', $questionnaireId)
										->with('hospital', $hospital)
										->with('project', $project)
										->with('optionsList', $optionsList)
										->with('questionsList', $questionsList);
	}

	public function StoreQuestions(Request $request,$hospitalSlug,$projectSlug,$questionnaireId)
	{

	  // try{
			$questionsType = $request->input("questionType");
			$titles = $request->input("title");
			$questions = $request->input("question");
			$questionIds = $request->input("questionId");
			$options = $request->input("option");
			$optionIds = $request->input("optionId");
			$scores = $request->input("score");

			$questionnaireObj = new ParseQuery("Questionnaire");
			$questionnaire = $questionnaireObj->get($questionnaireId);
			$previousQuestionObj = NULL;
			$nextQuestionObj = NULL;

			foreach ($questionsType as $key => $questionType) {
		
				$title = $titles[$key];
				$question = $questions[$key];
				$questionId = $questionIds[$key];
				$isChild = false;

				if($questionType=="" || $title=="" || $question=="")
					continue;

				if($questionId !="")
				{
				  $questionObject = new ParseQuery("Questions");
				  $questionObj = $questionObject->get($questionId);
				}
				else
				{
					$questionObj = new ParseObject("Questions");
					$questionObj->set('questionnaire',$questionnaire);
					$questionObj->set("previousQuestion",$previousQuestionObj);

					if($previousQuestionObj!=NULL)
					{
						$prevQuestionObject = new ParseQuery("Questions");
						$prevQuestionObj = $prevQuestionObject->get($previousQuestionObj->getObjectId());
						$prevQuestionObj->set('nextQuestion',$questionObj);
						$prevQuestionObj->save();
					}
					
				}
				
				$questionObj->set("question",$question);
				$questionObj->set('title',$title);
				$questionObj->set('isChild',$isChild);
				$questionObj->set('type',$questionType);
				$questionObj->save();

				$previousQuestionObj = $questionObj;

				 
				if(isset($options[$key]))
				{ 
				  $questionOptions = $options[$key]; 
				  $optionScores = $scores[$key];
				  $optionId = $optionIds[$key];

				  foreach ($questionOptions as $k => $option) {
					if($option=="")
					  continue;

					if($optionId[$k] !="")
					{
					  $optionObject = new ParseQuery("Options");
					  $optionObj = $optionObject->get($optionId[$k]);
					}
					else
					{
					  $optionObj = new ParseObject("Options");
					  $optionObj->set("question",$questionObj);
					}

					$score = intval($optionScores[$k]);
					
					$optionObj->set('score',$score);
					$optionObj->set('label',$option);
					$optionObj->save();
				  }
				}
				
			}

		//  } catch (\Exception $e) {
		//   Log::error($e->getMessage());
		//   abort(404);   
		// } 

	  return redirect(url($hospitalSlug .'/'. $projectSlug .'/configure-questions/'.$questionnaireId)); 

	} 

	public function deleteQuestion($hospitalSlug,$projectSlug,$questionId)
	{
		try{
				$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

				$hospital = $hospitalProjectData['hospital'];
				$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

				$project = $hospitalProjectData['project'];
				$projectId = intval($project['id']);

				$questionObj = new ParseQuery("Questions");
				$question = $questionObj->get($questionId);

				$optionObjs = new ParseQuery("Options");
				$optionObjs->equalTo("question",$question);
				$options = $optionObjs->find();

				if(!empty($options))
				{
					foreach ($options as $key => $option) {
						$option->destroy();
					}
				}

				$question->destroy();

		 } catch (\Exception $e) {
		  Log::error($e->getMessage());
		  abort(404);   
		} 

		return response()->json([
					'code' => 'delete_question',
					'message' => "question deleted",
						], 203);
	}

	public function deleteOption($hospitalSlug,$projectSlug,$optionId)
	{
	  try{

		  $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

		  $hospital = $hospitalProjectData['hospital'];
		  $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

		  $project = $hospitalProjectData['project'];
		  $projectId = intval($project['id']);

		  $optionObject = new ParseQuery("Options");
		  $optionObj = $optionObject->get($optionId);
		  $optionObj->destroy();

		 } catch (\Exception $e) {
			Log::error($e->getMessage());
			abort(404);   
		} 

		return response()->json([
					'code' => 'delete_option',
					'message' => "option deleted",
						], 203);
	}

	public function getQuestionsOrder($hospitalSlug,$projectSlug,$questionnaireId)
	{
		try
		{
				$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

				$hospital = $hospitalProjectData['hospital'];
				$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

				$project = $hospitalProjectData['project'];
				$projectId = intval($project['id']);

				$questionnaireObj = new ParseQuery("Questionnaire");
				$questionnaire = $questionnaireObj->get($questionnaireId);

				$questionObjs = new ParseQuery("Questions");
				$questionObjs->equalTo("questionnaire",$questionnaire);
				$questionObjs->ascending("createdAt");
				$questions = $questionObjs->find();

				$questionsList = $this->getSequenceQuestions($questions,true);
		  

		} catch (\Exception $e) {
		    Log::error($e->getMessage());
		    abort(404);   
		}      

		

		return view('project.order-questions')->with('active_menu', 'settings')
										->with('questionnaireId', $questionnaireId)
										->with('hospital', $hospital)
										->with('project', $project)
										->with('questionsList', $questionsList);
	}

	public function setQuestionsOrder(Request $request,$hospitalSlug,$projectSlug,$questionnaireId)
	{
		try
		{
		 
			$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

			$hospital = $hospitalProjectData['hospital'];
			$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

			$project = $hospitalProjectData['project'];
			$projectId = intval($project['id']);

			$questionnaireObj = new ParseQuery("Questionnaire");
			$questionnaire = $questionnaireObj->get($questionnaireId);

			$questionIds = $request->input("questionId");
			$submitType = $request->input("submitType");

			$path="";
			if($submitType=="publish")
			{
				$questionnaire->set("status","published");
				$questionnaire->save();
				$path= url($hospitalSlug .'/'. $projectSlug .'/questions-summary/'.$questionnaireId);
			}
			else
			{

				foreach ($questionIds as $key=> $questionId) {
					$previous = ($key-1);
					$next = ($key+1);

					$previousQuestion = NULL;
					if(isset($questionIds[$previous]))
					{
						$previousQuestionObj = new ParseQuery("Questions");
						$previousQuestion = $previousQuestionObj->get($questionIds[$previous]);
					}

					$nextQuestion = NULL;
					if(isset($questionIds[$next]))
					{
						$nextQuestionObj = new ParseQuery("Questions");
						$nextQuestion = $nextQuestionObj->get($questionIds[$next]);
					}
					 
					$questionObj = new ParseQuery("Questions");
					$question = $questionObj->get($questionId);

					$question->set("nextQuestion",$nextQuestion);
					$question->set("previousQuestion",$previousQuestion);

					$question->save();

				}

				$questionnaire->set("status","completed");
				$questionnaire->save();
				$path= url($hospitalSlug .'/'. $projectSlug .'/order-questions/'.$questionnaireId);
			}
			

			

		} catch (\Exception $e) {
		    Log::error($e->getMessage());
		    abort(404);   
		}  

		  return redirect($path); 
	}

}
