<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;

class TaskService 
{
	private TaskRepository $taskRepository;

	public function __construct() {
		$this->taskRepository = new TaskRepository();
	}

	/**
	 * NOTE: untuk mengambil semua tasks di collection task
	 */
	public function getTasks()
	{
		$tasks = $this->taskRepository->getAll();
		return $tasks;
	}

	/**
	 * NOTE: menambahkan task
	 */
	public function addTask(array $data)
	{
		$taskId = $this->taskRepository->create($data);
		return $taskId;
	}

	/**
	 * NOTE: UNTUK mengambil data task
	 */
	public function getById(string $taskId)
	{
		$task = $this->taskRepository->getById($taskId);
		return $task;
	}

	/**
	 * NOTE: untuk update task
	 */
	public function updateTask(array $editTask, array $formData)
	{
		if(isset($formData['title']))
		{
			$editTask['title'] = $formData['title'];
		}

		if(isset($formData['description']))
		{
			$editTask['description'] = $formData['description'];
		}

		$id = $this->taskRepository->save($editTask);
		return $id;
	}

	/**
	 * NOTE: untuk menghapus task
	 */
	public function deleteTask(string $taskId) : ?string
	{
		$task = $this->taskRepository->getById($taskId);

		if(!$task) {
			return null;
		}

		$taskTitle = $task['title'];
		$this->taskRepository->delete(['_id'=>$taskId]);

		return $taskTitle;
	}

	/**
	 * NOTE: untuk melakukan assign task
	 */
	public function assignTask(string $taskId, string $assigned) : ?array
	{
		$assignTask = $this->taskRepository->getById($taskId);

		if(!$assignTask) {
			return null;
		}

		if(isset($assignTask['assigned'])) {
			return array("message" => "Task ".$taskId." sedang dikerjakan oleh pengguna lain");
		} else {
			$assignTask['assigned'] = $assigned;	
			// array_push($assignTask['assigned'], $assigned);
		}

		$assignedTaskId = $this->taskRepository->save($assignTask);
		$task = $this->taskRepository->getById($assignedTaskId);
		return $task;
	}
}