<?php

declare (strict_types=1);
$activities = ["Slappat", "Kodat frontend", "Slötittat på YouTube", "Kodat backend", "Felsökt frontend", "Felsökt backend", "Kaffepaus", "Sökt information på nätet"];
unlink('./test.db');

$db = new PDO('sqlite:./test.db','charset=UTF8');
$db->query('PRAGMA foreign_keys = ON;');

$db->query('CREATE TABLE "activities" (
	"id"	INTEGER NOT NULL,
	"activity"	TEXT NOT NULL UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
)');
$db->query('CREATE TABLE "tasks" (
	"id"	INTEGER NOT NULL,
	"activityId"	INTEGER NOT NULL,
	"time"	INTEGER NOT NULL,
	"date"	TEXT NOT NULL,
	"description"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT),
	CONSTRAINT "fk_activityId" FOREIGN KEY("activityId") REFERENCES activities ("id") ON DELETE RESTRICT
)');

$stmt = $db->prepare('INSERT INTO activities (activity) values (:activity)');
foreach ($activities as $act) {
    $stmt->execute([':activity' => $act]);
}
$stmt->execute([':activity'=>'Ska raderas']);

$date = new DateTimeImmutable();
$stmt = $db->prepare('INSERT INTO tasks (id, activityId, date, time, description) values (:id, :activityId, :date, :time, :description)');
for ($ix = 0; $ix < 70; $ix++) {
    $i = rand(1, 15);
    $rec = [];
    $rec["id"] = $ix+1;
    $rec["activityId"] = $i % count($activities)+1;
    $rec["date"] = $date->format("Y-m-d");
    $rec["time"] = rand(3, 8) * 15;
    $rec["description"] = "Fritext ";
    $date = $date->sub(new DateInterval("P{$i}D"));
    $stmt->execute($rec);
}

$fil="./test.db";

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="test.db"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
