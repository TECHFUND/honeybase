<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
include "Seeds.php";

class DatabaseSeeder extends Seeder {
	public function run() {
		Model::unguard();
		$this->call('MyPageSeeder');
	}

}
