package net;

import java.awt.Component;
import java.net.URLConnection;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.Arrays;

import javax.swing.JOptionPane;

import util.Constants;

public class Network {
	public static final int FAILURE = -1;
	public static final int SUCCESS = 0;
	
	public static int createDatabase(Component c, String room, char[] roomPassword, String admin, char[] adminPassword) {
		ArrayList<String> keys = new ArrayList<>();
		keys.add(Constants.INDEX_KEY_REQUEST);
		keys.add(Constants.INDEX_KEY_ROOM);
		keys.add(Constants.INDEX_KEY_ROOM_PASSWORD);
		keys.add(Constants.INDEX_KEY_ROOM_SALT);
		keys.add(Constants.INDEX_KEY_ADMIN);
		keys.add(Constants.INDEX_KEY_ADMIN_PASSWORD);
		
		ArrayList<String> data = new ArrayList<>();
		String hash;
		data.add(Constants.REQUEST_CREATE);
		data.add(room);
		
		/*Alright, story time. So the method for getting passwords as Strings from the JPasswordField is deprecated
		because Strings are immutable and can still be in memory until the GarbageCollector removes it (at an 
		unknown time). So, instead, JPasswordFields return a char[] so you can write over it with zeros when
		you are done using it because char[] are mutable unlike a String where setting it to "" will just move the pointer.
		I have used the non deprecated method, made sure that I can zero the password later, made sure the password
		is actually zeroed, AND taken secure methods to do so. But there is a catch... In the end, the plain-text
		password DOES get zeroed... but the SHA512 hash will stay in memory until the GarbageCollector grabs it. This
		clearly defeats the purpose of char[] passwords but it cannot be solved as the password needs to be sent over
		the Internet as a string.*/
		
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
		
		hash = Constants.getSHA512Hash(Constants.toBytes(roomPassword), userSalt);
		data.add(hash);
		data.add(userSalt);
		data.add(admin);
		hash = Constants.getSHA512Hash(Constants.toBytes(adminPassword), adminSalt);
		data.add(hash);
		Arrays.fill(roomPassword, '\u0000'); // clear sensitive data
		Arrays.fill(adminPassword, '\u0000'); // clear sensitive data
		
		URLConnection con;
		ArrayList<String> ret = new ArrayList<>();
		try {
			con = Sender.putData(keys,data);
			ret = Getter.getData(con);
		} catch (Exception e) {
			throwError(c,e,false);
			return FAILURE;
		}
		return calculateSuccess(c,ret);
	}
	
	private static void throwError(Component c, Exception e, boolean internal) {
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

	public static int testLogin(Component c, String room, char[] password, String h) {
		ArrayList<String> keys = new ArrayList<>();
		keys.add(Constants.INDEX_KEY_REQUEST);
		keys.add(Constants.INDEX_KEY_ROOM);
		keys.add(Constants.INDEX_KEY_ROOM_PASSWORD);
		
		ArrayList<String> data = new ArrayList<>();
		String hash;
		String userSalt = "";
		try {
			userSalt = Getter.getSaltFromDatabase(room);
		} catch (Exception e) {
			throwError(c,e,false);
			return FAILURE;
		}
		hash = Constants.getSHA512Hash(Constants.toBytes(password), userSalt);
		try {
			h = hash;
		} catch (Exception e) {
			e.printStackTrace();
		}
		Arrays.fill(password, '\u0000'); // clear sensitive data
		data.add(Constants.REQUEST_LOGIN);
		data.add(room);
		data.add(hash);
		
		URLConnection con;
		ArrayList<String> ret = new ArrayList<>();
		try {
			con = Sender.putData(keys,data);
			ret = Getter.getData(con);
		} catch (Exception e) {
			throwError(c,e,false);
			return FAILURE;
		}
		
		return calculateSuccess(c,ret);
	}
	
	private static int calculateSuccess(Component c, ArrayList<String> ret) {
		if(ret.size() > 0 && ret.get(0).equals("OK")) {
			return SUCCESS;
		} else if (ret.size() > 0){
			throwError(c,new Exception(ret.get(0)), false);
			for(String s : ret) {
				System.out.println("Server said: " + s);
			}
			return FAILURE; //We can never win...
		} else {
			throwError(c,new IndexOutOfBoundsException("The server gave an empty reply!"), false);
			return FAILURE; //We REALLY can never win...
		}
	}
	
}
