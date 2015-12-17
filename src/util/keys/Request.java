package util.keys;

public class Request {
	private Request() throws InstantiationException{
		throw new InstantiationException();
	}
	public static final String SALT="SALTY_MC_SALTER";
	public static final String CREATE="CREATE_ROOM";
	public static final String LOGIN="LOGIN";
	public static final String SETDATA = "PLS_CREATE_DATA";
}
