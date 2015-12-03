package util;

public class Constants {
	private Constants() throws InstantiationException{
		throw new InstantiationException();
	}
	
	public static final String DATABASE_PHP_URL="http://attendance.yhscs.us/db.php";
	
	public static final String ROOM_INDEX_KEY="ROOM";
	public static final String ROOM_PASSWORD_INDEX_KEY="ROOM_PASSWORD";
	public static final String ADMIN_PASSWORD_INDEX_KEY="ADMINISTRATOR_PASSWORD";
	
}
