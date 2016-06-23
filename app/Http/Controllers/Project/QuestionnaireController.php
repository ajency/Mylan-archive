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

	public function getSequenceQuestions($questions,$subQuestionsFlag=false,$includeSubQuestion=true)
	{

		$questionsList = [];
		$sequenceQuestions = [];
		$subQuestions = [];
		$questionConditions = [];
		foreach ($questions as   $question) {
			$questionId = $question->getObjectId();
			$nextQuestionId = (!is_null($question->get('nextQuestion')))? $question->get('nextQuestion')->getObjectId():'';
			$previousQuestionId = (!is_null($question->get('previousQuestion')))? $question->get('previousQuestion')->getObjectId():'';
			
			if(!is_null($question->get('condition')))
			{
				$conditions = $question->get('condition');
				foreach ($conditions as $key => $condition) {
					$questionConditions[$condition['optionId']] = $condition['questionId'];
				}
				
			}  
			
			$questionType = $question->get('type');
			$title = $question->get('title');
			$name = $question->get('question');
			$isChild = $question->get('isChild');
			if(!$isChild)
				$questionsList[$questionId] = ['nextQuestionId'=>$nextQuestionId,'question'=>$name,'title'=>$title,'type'=>$questionType,'condition'=>$questionConditions];
			elseif($subQuestionsFlag)
				$subQuestions[$previousQuestionId][$questionId] = ['previousQuestionId'=>$previousQuestionId,'question'=>$name,'title'=>$title,'type'=>$questionType];


		}

		
		$firstQuestionId = $this->getFirstQuestion($questions);

		$orderQuestions = (!empty($questionsList))? $this->orderQuestions($questionsList,$firstQuestionId,[]) :[];

		if(!$includeSubQuestion) 
			$orderQuestions['parentQuestions'] = $orderQuestions;

		if(!empty($subQuestions))
		{
			if($includeSubQuestion)
				$orderQuestions = $this->addSubQuestionToList($orderQuestions,$subQuestions);
			else
			{
				$orderQuestions['subQuestions'] = $subQuestions;
			}
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

		  $nextPage = "yes";
		  $questionnaireId ="";
		  $settings =[];
		  $settings['frequency']['day'] = '1'; 
		  $settings['frequency']['hours'] = '0'; 
		  $settings['gracePeriod']['day'] = '';
		  $settings['gracePeriod']['hours'] = '8';
		  $settings['reminderTime']['day'] = '';
		  $settings['reminderTime']['hours'] = '1';
		  $settings['editable'] = '';
		  $settings['type'] = ''; 
		  $settings['name'] = ''; 
		  $settings['pauseProject'] = '';  
		  $settings['status'] = '';  
		  
		  $action ="store-questionnaire-setting";
		  if(!empty($questionnaire))
		  {
		  	$nextPage = "no";
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
			exceptionError($e);  
		}      


		return view('project.questionnaire-setting')->with('active_menu', 'settings')
										->with('hospital', $hospital)
										->with('project', $project)
										->with('action', $action)
										->with('nextPage', $nextPage)
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
			$nextPage = $request->input('next_page');   

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
			$questionnaireId = $questionnaire->getObjectId();

	 
			$schedule = new ParseObject("Schedule");
			$schedule->set("questionnaire", $questionnaire);
			$schedule->set("frequency", $frequency);
			$schedule->save();

			Session::flash('success_message','Project settings successfully created.');
			
		} catch (\Exception $e) {
			exceptionError($e);           
		}
 
		return redirect(url($hospitalSlug .'/'. $projectSlug .'/configure-questions/'.$questionnaireId)); 

		// return redirect(url($hospitalSlug .'/'. $projectSlug .'/questionnaire-setting')); 
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
			$redirectUrl = $request->input("redirect_url"); 

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

			exceptionError($e);           
		}
		// return redirect(url($hospitalSlug .'/'. $projectSlug .'/questionnaire-setting')); 
		if($redirectUrl=='')
			return redirect(url($hospitalSlug .'/'. $projectSlug .'/questionnaire-setting')); 
		else
			return redirect($redirectUrl);
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

			

			$questionsList = $this->getSequenceQuestions($questions,true,false);  

			$subQuestions = (isset($questionsList['subQuestions'])) ? $questionsList['subQuestions']:[];  
			$questionsList = (isset($questionsList['parentQuestions'])) ?$questionsList['parentQuestions']:[]; 


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
			exceptionError($e);     
		}      

		

		return view('project.questions-summary')->with('active_menu', 'settings')
										->with('questionnaireId', $questionnaireId)
										->with('hospital', $hospital)
										->with('project', $project)
										->with('optionsList', $optionsList)
										->with('subQuestions', $subQuestions)
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

			$questionnaireName = $questionnaire->get("name");

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
 
			$questionsList = $this->getSequenceQuestions($questions,true,false);  

			$subQuestions = (isset($questionsList['subQuestions'])) ? $questionsList['subQuestions']:[];  
			$questionsList = (isset($questionsList['parentQuestions'])) ?$questionsList['parentQuestions']:[]; 


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
			exceptionError($e);     
		}      


		return view('project.configure-questions')->with('active_menu', 'settings')
										->with('questionnaireId', $questionnaireId)
										->with('hospital', $hospital)
										->with('project', $project)
										->with('questionnaireName', $questionnaireName)
										->with('optionsList', $optionsList)
										->with('subQuestions', $subQuestions)
										->with('questionsList', $questionsList);
	}

	//TODO : optimize code  

	public function StoreQuestions(Request $request,$hospitalSlug,$projectSlug,$questionnaireId)
	{

	  try{
	  		// dd($request->all());
			$questionsType = $request->input("questionType");
			$titles = $request->input("title");
			$questions = $request->input("question");
			$questionIds = $request->input("questionId");
			$options = $request->input("option");
			$optionIds = $request->input("optionId");
			$scores = $request->input("score");
			$optionKeys = $request->input("optionKeys");
			$subquestionType = $request->input("subquestionType");
			$subquestionTitle = $request->input("subquestionTitle");
			$subquestion = $request->input("subquestion");
			$redirectUrl = $request->input("redirect_url");

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

				$parentQuestionObj = $this->saveQuestion($questionType, $title, $question, $isChild, $questionId, $questionnaire, $previousQuestionObj);

				$previousQuestionObj = $parentQuestionObj;

				 //options
				$optionQuestionIds =[];
				if(isset($options[$key]))
				{ 
				  $questionOptions = $options[$key]; 
				  $optionScores = $scores[$key];
				  $optionId = $optionIds[$key];

				  // $optionQuestionIds = $this->saveOptions($key,$questionnaire,$questionObj,$previousQuestionObj,$questionOptions,$optionScores,$optionId,true,$questionIds,$optionIds,$optionKeys,$subquestionType,$subquestionTitle,$subquestion);

				  	foreach ($questionOptions as $k => $option)
				  	{
						if($option=="")
						  continue;

						$score = intval($optionScores[$k]);
					
						$optionObj = $this->saveOption($optionId[$k],$parentQuestionObj,$score,$option);
						$optionObjectId = $optionObj->getObjectId();

						//**SAVE SUB QUESTION ***
						
						if(isset($optionKeys[$key][$k]))
						{
							$subquestionKey = $optionKeys[$key][$k];

							$questionType = $subquestionType[$subquestionKey];
							$questionTitle = $subquestionTitle[$subquestionKey];
							$question = $subquestion[$subquestionKey];
							$subquestionId = $questionIds[$subquestionKey];
							$isChild = true;

							$subQuestionObj = $this->saveQuestion($questionType, $questionTitle, $question, $isChild, $subquestionId, $questionnaire, $previousQuestionObj,false);
							$subQuestionObjectId = $subQuestionObj->getObjectId(); 
							$optionQuestionIds[] =['optionId'=>$optionObjectId,'questionId'=>$subQuestionObjectId];

							if(isset($options[$subquestionKey]))
							{ 
								$subQuestionOptions = $options[$subquestionKey]; 
								$subQuestionOptionScores = $scores[$subquestionKey];
								$subQuestionOptionId = $optionIds[$subquestionKey];

							  	foreach ($subQuestionOptions as $sk => $subQuestionOption) {
									if($subQuestionOption=="")
									  continue;

									$score = intval($subQuestionOptionScores[$sk]);
								
									$optionObj = $this->saveOption($subQuestionOptionId[$sk],$subQuestionObj,$score,$subQuestionOption);
							  	}
							}

							
							 
						}
						// ***


				  	}	
				
				}

				/*Sub question condition
				 [{"optionId":"ScCtxfHL5W","questionId":"iYycS8dwYj"}]
				*/
				 if(!empty($optionQuestionIds))
				 {
				 	$parentQuestionObj->setArray('condition',$optionQuestionIds);
				 	$parentQuestionObj->save();
				 }
				 

				
			}
			// dd($request->all());
			Session::flash('success_message','Project Questionnaire successfully updated.');

		 } catch (\Exception $e) {
		  exceptionError($e);     
		} 

		if($redirectUrl=='')
			return redirect(url($hospitalSlug .'/'. $projectSlug .'/configure-questions/'.$questionnaireId)); 
		else
			return redirect($redirectUrl);

	} 

	public function saveQuestion($questionType, $title, $question, $isChild, $questionId, $questionnaire, $previousQuestionObj, $isParent=true)
	{
		
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
			$questionObj->set('nextQuestion',NULL);

			if($previousQuestionObj!=NULL && $isParent)
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

		return $questionObj;
	}

	public function saveOptions($key,$questionnaire,$questionObj,$previousQuestionObj,$questionOptions,$optionScores,$optionId,$hasSubQuestion=true,$questionIds=[],$optionIds=[],$optionKeys=[],$subquestionType=[],$subquestionTitle=[],$subquestion=[])
	{
		$optionQuestionIds =[];
		foreach ($questionOptions as $k => $option)
	  	{
			if($option=="")
			  continue;

			$score = intval($optionScores[$k]);
		
			$optionObj = $this->saveOption($optionId[$k],$questionObj,$score,$option);
			$optionOjectId = $optionObj->getObjectId();

			//**SAVE SUB QUESTION ***
			
			if($hasSubQuestion==true)
			{
				if(isset($optionKeys[$key][$k]))
				{
					$subquestionKey = $optionKeys[$key][$k];

					$questionType = $subquestionType[$subquestionKey];
					$questionTitle = $subquestionTitle[$subquestionKey];
					$question = $subquestion[$subquestionKey];
					$subquestionId = $questionIds[$subquestionKey];
					$isChild = true;

					$subQuestionObj = $this->saveQuestion($questionType, $questionTitle, $question, $isChild, $subquestionId, $questionnaire, $previousQuestionObj,false);
					$subQuestionObjectId = $subQuestionObj->getObjectId(); 

					if(isset($options[$subquestionKey]))
					{ 
						$subQuestionOptions = $options[$subquestionKey]; 
						$subQuestionOptionScores = $scores[$subquestionKey];
						$subQuestionOptionId = $optionIds[$subquestionKey];

						$saveOptions = $this->saveOptions($key,$subQuestionObj,null,$subQuestionOptions,$subQuestionOptionScores,$subQuestionOptionId,false);
					}

					$optionQuestionIds[] =['optionId'=>$optionOjectId,'questionId'=>$subQuestionObjectId];

				}
			}
			
			
	  	}

	  	return $optionQuestionIds;
	}

	public function saveOption($optionId,$questionObj,$score,$option)
	{
		if($optionId !="")
		{
		  $optionObject = new ParseQuery("Options");
		  $optionObj = $optionObject->get($optionId);
		}
		else
		{
		  $optionObj = new ParseObject("Options");
		  $optionObj->set("question",$questionObj);
		}
		
		$optionObj->set('score',$score);
		$optionObj->set('label',$option);
		$optionObj->save();

		return $optionObj;
	}

	public function deleteQuestion($hospitalSlug,$projectSlug,$questionId)
	{
		try{
				$hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

				$hospital = $hospitalProjectData['hospital'];
				$logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

				$project = $hospitalProjectData['project'];
				$projectId = intval($project['id']);

				$deleteQuestion = $this->deleteQuestionAndOptions($questionId);
				

		 } catch (\Exception $e) {
		  exceptionError($e);       
		} 

		return response()->json([
					'code' => 'delete_question',
					'message' => "question deleted",
						], 203);
	}

	public function deleteQuestionAndOptions($questionId)
	{
		$questionObj = new ParseQuery("Questions");
		$questionObj->includeKey("previousQuestion");
		$question = $questionObj->get($questionId);

		$nextQuestionObj = $question->get("nextQuestion");  
		$previousQuestionObj = $question->get("previousQuestion");
		$conditions = $question->get("condition");
		$isChild = $question->get("isChild");
		
		if(!$isChild)
		{  
			if(!empty($conditions))
			{
				foreach ($conditions as $key => $condition) {
					$subquestionId = $condition['questionId'];
					$deleteQuestion = $this->deleteQuestionAndOptions($subquestionId);
				}
			}	
		 
			//REST SEQUENCE
			if($nextQuestionObj!=null)
			{
				$nextQuestionObj->set("previousQuestion",$previousQuestionObj);
				$nextQuestionObj->save();
			}
			
			if($previousQuestionObj!=null)
			{
				$previousQuestionObj->set("nextQuestion",$nextQuestionObj);
				$previousQuestionObj->save();
			}
		}
		else
		{
			//remove sub question id from parent question condition
			$parentQuestionsObj = $previousQuestionObj;
			$parentQuestionsCondition = $parentQuestionsObj->get("condition");
			$optionQuestionIds = [];
			foreach ($parentQuestionsCondition as $key => $parentQuestionCondition) {
				if($questionId != $parentQuestionCondition['questionId'])
				{
					$optionQuestionIds[] = $parentQuestionCondition;
				}
				 
			}

			$parentQuestionsObj->setArray('condition',$optionQuestionIds);
			$parentQuestionsObj->save();


		}

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

		return true;
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
				$optionObject->includeKey("question");
				$optionObj = $optionObject->get($optionId);

				$question = $optionObj->get('question'); 
				$conditions = $question->get("condition");
				$isChild = $question->get("isChild");

				//get options count for the question (server side verification)

				$questionOptions = new ParseQuery("Options");
				$questionOptions->equalTo("question",$question);
				$optionsCount = $questionOptions->count(); 

				if($optionsCount > 1)
				{
					$updatedCondition = [];
					if(!$isChild)
					{  
						if(!empty($conditions))
						{
							foreach ($conditions as $key => $condition) {
								$subquestionId = $condition['questionId'];
								$questionOptionId = $condition['optionId'];
								if($optionId == $questionOptionId)
									$deleteQuestion = $this->deleteQuestionAndOptions($subquestionId);
								else
									$updatedCondition[]=$condition;
							}

							$question->setArray('condition',$updatedCondition);
					 		$question->save();
						}
						
					}
		 
					$optionObj->destroy();

					$message = "option deleted";
					$statusCode = 203;
				}
				else
				{
					$message = "failed option deleted";
					$statusCode = 200;
				}

				

		 	} catch (\Exception $e) {
				exceptionError($e);    
			} 

		return response()->json([
					'code' => 'delete_option',
					'message' => $message,
						], $statusCode);
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

			$questionnaireName = $questionnaire->get("name");

			$questionObjs = new ParseQuery("Questions");
			$questionObjs->equalTo("questionnaire",$questionnaire);
			$questionObjs->ascending("createdAt");
			$questions = $questionObjs->find();

			$questionsList = $this->getSequenceQuestions($questions);
			 
		  

		} catch (\Exception $e) {
		    exceptionError($e);      
		}      

		

		return view('project.order-questions')->with('active_menu', 'settings')
										->with('questionnaireId', $questionnaireId)
										->with('hospital', $hospital)
										->with('questionnaireName', $questionnaireName)
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
			$redirectUrl = $request->input("redirect_url"); 

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
			

		Session::flash('success_message','Project Questionnaire successfully ordered.');	

		} catch (\Exception $e) {
		    exceptionError($e);      
		}  

		
		if($redirectUrl=='')
			return redirect($path); 
		else
			return redirect($redirectUrl);
			  
	}


}
