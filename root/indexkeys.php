<?php
class Index {
	const REQUEST="REQUEST";
	const ROOM="ROOM";
	const ROOM_PASSWORD="ROOM_PASSWORD";
	const ROOM_SALT="ROOM_SALT";
	const ADMIN="ADMIN";
	const ADMIN_PASSWORD="ADMINISTRATOR_PASSWORD";
	const USER="USER";
}

class StudentData {
	const ID="STUDID";
	const CHECK_TIME="STUDTIME";
	const AUTOMATIC="STUDAUTO_LOGOUT";
	const IS_CHECKIN="IS_CHECKIN";
	const PERIOD="PERIOD";
}

class Request {
	const SALT = "SALTY_MC_SALTER";
	const CREATE = "CREATE_ROOM";
	const LOGIN = "LOGIN";
	const SETDATA = "PLS_CREATE_DATA";
}
?>