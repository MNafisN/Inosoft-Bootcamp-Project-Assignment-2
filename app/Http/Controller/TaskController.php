<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller 
{
	private TaskService $taskService;

	public function __construct() {
		$this->taskService = new TaskService();
	}

	public function showTasks()
	{
		$tasks = $this->taskService->getTasks();
		return response()->json($tasks, 200);
	}

	public function createTask(Request $request)
	{
		$request->validate([
			'title'=>'required|string|min:3',
			'description'=>'required|string'
		]);

		$data = [
			'title'=>$request->post('title'),
			'description'=>$request->post('description')
		];

		$dataSaved = [
			'title'=>$data['title'],
			'description'=>$data['description'],
			'assigned'=>null,
			'subtasks'=> [],
			'created_at'=>time()
		];

		$id = $this->taskService->addTask($dataSaved);
		$task = $this->taskService->getById($id);

		return response()->json([
			'message' => 'Task ditambahkan',
			'task_data' => $task
		], 200);
	}


	public function updateTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required|string',
			'title'=>'string',
			'description'=>'string',
			'assigned'=>'string',
			'subtasks'=>'array',
		]);

		$taskId = $request->post('task_id');
		$formData = $request->only('title', 'description', 'assigned', 'subtasks');
		$task = $this->taskService->getById($taskId);

		$this->taskService->updateTask($task, $formData);

		$task = $this->taskService->getById($taskId);

		return response()->json([
			'message' => 'Task diperbarui',
			'task_data' => $task
		], 200);
	}


	// TODO: deleteTask()
	public function deleteTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required'
		]);

		$taskId = $request->task_id;
		$task = $this->taskService->deleteTask($taskId);

		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 404);
		}

		return response()->json([
			'message'=> 'Success delete task titled : '.$task
		], 200);
	}

	// TODO: assignTask()
	public function assignTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required',
			'assigned'=>'required'
		]);

		$taskId = $request->get('task_id');
		$assigned = $request->post('assigned');

		$task = $this->taskService->assignTask($taskId, $assigned);

		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 404);
		}

		return response()->json([
			"message" => "Sukses menugaskan pengguna ".$assigned." ke task ".$taskId
		], 200);
	}

	// TODO: unassignTask()
	public function unassignTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required'
		]);

		$taskId = $request->post('task_id');

		$task = $this->taskService->unassignTask($taskId);

		if(!$task)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 404);
		}

		return response()->json([
			"message" => "Pengguna yang ditugaskan pada task ".$taskId." sudah selesai bertugas"
		], 200);
	}

	// TODO: createSubtask()
	public function createSubtask(Request $request)
	{
		$request->validate([
			'task_id'=>'required',
			'title'=>'required|string',
			'description'=>'required|string'
		]);

		$data = $request->only('task_id', 'title', 'description');

		$taskQuery = $this->taskService->getById($data['task_id']);

		if(!$taskQuery)
		{
			return response()->json([
				"message"=> "Task ".$data['task_id']." tidak ada"
			], 404);
		}

		$task = $this->taskService->createSubtask($taskQuery, $data);

		return response()->json([
			'message' => 'Subtask ditambahkan',
			'task_data' => $task
		], 200);
	}

	// TODO deleteSubTask()
	public function deleteSubtask(Request $request)
	{
		$request->validate([
			'task_id'=>'required',
			'subtask_id'=>'required'
		]);

		$data = $request->only('task_id', 'subtask_id');

		$taskQuery = $this->taskService->getById($data['task_id']);

		if(!$taskQuery)
		{
			return response()->json([
				"message"=> "Task ".$data['task_id']." tidak ada"
			], 404);
		}

		$task = $this->taskService->deleteSubtask($taskQuery, $data);

		return response()->json([
			"message" => "Subtask ".$data['subtask_id']." telah dihapus",
			"task_data" => $task
		], 200);
	}
}