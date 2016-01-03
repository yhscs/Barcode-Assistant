package util.keys;

public class StudentData {
	private StudentData() throws InstantiationException{
		throw new InstantiationException();
	}
	public static final String ID="STUDID";
	public static final String CHECK_TIME="STUDTIME";
	public static final String AUTOMATIC="STUDAUTO_LOGOUT";
	public static final String IS_CHECIN = "IS_CHECKIN";
}
