<?php
namespace App\ContohBootcamp\Repositories;

use App\Helpers\MongoModel;

class TaskRepository
{
	private MongoModel $tasks;
	
	public function __construct()
	{
		$this->tasks = new MongoModel('tasks');
	}

	/**
	 * Untuk mengambil semua tasks
	 */
	public function getAll()
	{
		$tasks = $this->tasks->get([]);
		return $tasks;
	}

	/**
	 * Untuk membuat task
	 */
	public function create(array $data)
	{
		$dataSaved = [
			'title'=>$data['title'],
			'description'=>$data['description'],
			'assigned'=>null,
			'subtasks'=> [],
			'created_at'=>time()
		];

		$id = $this->tasks->save($dataSaved);
		return $id;
	}

	/**
	 * Untuk mendapatkan task bedasarkan id
	 *  */
	public function getById(string $id)
	{
		$task = $this->tasks->find(['_id'=>$id]);
		return $task;
	}

	/**
	 * Untuk menyimpan task baik untuk membuat baru atau menyimpan dengan struktur bson secara bebas
	 *  */
	public function save(array $editedData)
	{
		$id = $this->tasks->save($editedData);
		return $id;
	}

	/**
	 * Untuk menghapus task bedasarkan id
	 *  */
	public function delete(array $deleteData)
	{
		$this->tasks->deleteQuery($deleteData);
	}

	/**
	 * Untuk membuat Subtask
	 */
	public function createSubtask(array $task, array $data)
	{
		$subtasks = isset($task['subtasks']) ? $task['subtasks'] : [];

		$subtasks[] = [
			'_id'=> (string) new \MongoDB\BSON\ObjectId(),
			'title'=>$data['title'],
			'description'=>$data['description']
		];

		$task['subtasks'] = $subtasks;

		$id = $this->tasks->save($task);
		return $id;
	}

	/**
	 * Untuk menghapus Subtask
	 */
	public function deleteSubtask(array $task, array $data)
	{
		$subtasks = isset($task['subtasks']) ? $task['subtasks'] : [];
		$subtaskId = $data['subtask_id'];

		// Pencarian dan penghapusan subtask
		$subtasks = array_filter($subtasks, function($subtask) use($subtaskId) {
			if($subtask['_id'] == $subtaskId) {
				return false;
			} else {
				return true;
			}
		});

		$subtasks = array_values($subtasks);
		$task['subtasks'] = $subtasks;

		$id = $this->tasks->save($task);
		return $id;
	}
}