package net;

import java.awt.Component;
import java.net.URLConnection;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Arrays;

import javax.swing.JOptionPane;

import util.Bell;
import util.Constants;
import util.HashAndReturn;
import util.keys.Indexes;
import util.keys.Request;
import util.keys.StudentData;

public class Network {
	public static final int FAILURE = -1;
	public static final int SUCCESS = 0;
	
	/**
	 * Puts some data about check in and check out times on the webserver's database. 
	 * @param c The component to use if an error is returned. This will make an error message pop up on the frame if there is one.
	 * @param room The string of the room that we are using. This acts like the username
	 * @param roomHash The string of the 
	 * @param id String of the student ID.
	 * @param time Time string (In PHP/SQL format!!) of the time the data was sampled.
	 * @param automatic Boolean that is true if the student is being logged out automatically. 
	 * @return An int SUCCESS or FAILURE.
	 */
	public static int putData(String room, String roomHash, String id, String time, int hour, int minute, boolean automatic, boolean isCheckin) throws Exception {
		
		//--PREPARE DATA--
		ArrayList<String> data = new ArrayList<>();		ArrayList<String> keys = new ArrayList<>();
		keys.add(Indexes.REQUEST);						data.add(Request.SETDATA);
		keys.add(Indexes.ROOM);							data.add(room);
		keys.add(Indexes.ROOM_PASSWORD);				data.add(roomHash);
		keys.add(StudentData.ID);						data.add(id);
		keys.add(StudentData.CHECK_TIME);				data.add(time);
		keys.add(StudentData.AUTOMATIC);				data.add((automatic ? "1" : "0"));
		keys.add(StudentData.PERIOD);					data.add(Bell.getBell(hour, minute));
		keys.add(StudentData.IS_CHECIN);				data.add((isCheckin ? "1" : "0")); //TODO: This
		//--THAT IS ALL--
		return calculateSuccessThrowable(keys,data); // send data
	}
	
	/**
	 * 
	 * @param c
	 * @param room
	 * @param roomPassword
	 * @param admin
	 * @param adminPassword
	 * @return
	 */
	public static int createDatabase(Component c, String room, char[] roomPassword, String admin, char[] adminPassword) {
		String userSalt;
		try {
			userSalt = Constants.getSalt();
		} catch (NoSuchAlgorithmException e1) {
			throwError(c,e1,true);
			return FAILURE;
		}
		String adminSalt = "";
		try {
			adminSalt = Getter.getSaltFromDatabase(admin);
		} catch (Exception e) {
			throwError(c,e,false);
			return FAILURE;
		}
		String roomHash = Constants.getSHA512Hash(Constants.toBytes(roomPassword), userSalt);
		String adminHash = Constants.getSHA512Hash(Constants.toBytes(adminPassword), adminSalt);
		
		
		//--PREPARE DATA--
		ArrayList<String> data = new ArrayList<>();		ArrayList<String> keys = new ArrayList<>();
		keys.add(Indexes.REQUEST);						data.add(Request.CREATE);
		keys.add(Indexes.ROOM);							data.add(room);
		keys.add(Indexes.ROOM_PASSWORD);				data.add(roomHash);
		keys.add(Indexes.ROOM_SALT);					data.add(userSalt);
		keys.add(Indexes.ADMIN);						data.add(admin);
		keys.add(Indexes.ADMIN_PASSWORD);				data.add(adminHash);
		//--THAT IS ALL--
		
		
		Arrays.fill(roomPassword, '\u0000'); // clear sensitive data
		Arrays.fill(adminPassword, '\u0000'); // clear sensitive data
		return calculateSuccess(c,keys,data); // send data
	}
	
	/**
	 * 
	 * @param c
	 * @param room
	 * @param password
	 * @param h
	 * @return
	 */
	public static HashAndReturn testLogin(Component c, String room, char[] password) {
		String userSalt = "";
		try {
			userSalt = Getter.getSaltFromDatabase(room);
		} catch (Exception e) {
			throwError(c,e,false);
			return new HashAndReturn(FAILURE, null);
		}
		String roomHash = Constants.getSHA512Hash(Constants.toBytes(password), userSalt);
		
		
		//--PREPARE DATA--
		ArrayList<String> keys = new ArrayList<>();		ArrayList<String> data = new ArrayList<>();
		keys.add(Indexes.REQUEST);						data.add(Request.LOGIN);
		keys.add(Indexes.ROOM);							data.add(room);
		keys.add(Indexes.ROOM_PASSWORD);				data.add(roomHash);
		//--THAT IS ALL--
		
		
		Arrays.fill(password, '\u0000'); // clear sensitive data
		return new HashAndReturn(calculateSuccess(c,keys,data), roomHash); //send data
	}
	
	/**
	 * 
	 * @param c
	 * @param e
	 * @param internal
	 */
	private static void throwError(Component c, Exception e, boolean internal){
		if(internal) {
			JOptionPane.showMessageDialog(c,
				"Internal error: " + e.toString() + "\nCheck your spelling or try again later.",
				"Error!",
				 JOptionPane.ERROR_MESSAGE);
		} else {
			JOptionPane.showMessageDialog(c,
			    "Server error: " + e.toString() + "\nTry again later. If the issue persists, contact the attendance system administrator.",
			    "Error!",
			    JOptionPane.ERROR_MESSAGE);
		}
	}

	/**
	 * 
	 * @param c
	 * @param keys
	 * @param data
	 * @return
	 */
	private static int calculateSuccess(Component c, ArrayList<String> keys, ArrayList<String> data) {
		URLConnection con;
		ArrayList<String> ret = new ArrayList<>();
		try {
			con = Sender.putData(keys,data);
			ret = Getter.getData(con);
		} catch (Exception e) {
			throwError(c,e,false);
			return FAILURE;
		}
		if(ret.size() > 0 && ret.get(0).equals("OK")) {
			return SUCCESS;
		} else if (ret.size() > 0){
			throwError(c,new Exception((ret.get(0).equals("<br />") ? "There was a PHP error. Get a programmer to fix it." : ret.get(0))), false);
			for(String s : ret) {
				System.out.println("Server said: " + s);
			}
			return FAILURE; //We can never win...
		} else {
			throwError(c,new IndexOutOfBoundsException("The server gave an empty reply!"), false);
			return FAILURE; //We REALLY can never win...
		}
	}
	
	private static int calculateSuccessThrowable(ArrayList<String> keys, ArrayList<String> data) throws Exception{
		URLConnection con;
		ArrayList<String> ret = new ArrayList<>();
		con = Sender.putData(keys,data);
		ret = Getter.getData(con);
		if(ret.size() > 0 && ret.get(0).equals("OK")) {
			return SUCCESS;
		} else if (ret.size() > 0){
			Exception e = new Exception((ret.get(0).equals("<br />") ? "There was a PHP error. Get a programmer to fix it." : ret.get(0)));
			for(String s : ret) {
				System.out.println("Server said: " + s);
			}
			throw e;
		} else {
			throw new IndexOutOfBoundsException("The server gave an empty reply!");
		}
	}
	
	/*Alright, story time. So the method for getting passwords as Strings from the JPasswordField is deprecated
	because Strings are immutable and can still be in memory until the GarbageCollector removes it (at an 
	unknown time). So, instead, JPasswordFields return a char[] so you can write over it with zeros when
	you are done using it because char[] are mutable unlike a String where setting it to "" will just move the pointer.
	I have used the non deprecated method, made sure that I can zero the password later, made sure the password
	is actually zeroed, AND taken secure methods to do so. But there is a catch... In the end, the plain-text
	password DOES get zeroed... but the SHA512 hash will stay in memory until the GarbageCollector grabs it. This
	clearly defeats the purpose of char[] passwords but it cannot be solved as the password needs to be sent over
	the Internet as a string.*/
}
