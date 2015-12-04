package net;

import java.awt.Component;
import java.io.IOException;
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
			return FAILURE;
		}
		String adminSalt = "";
		try {
			adminSalt = Getter.getSaltFromDatabase(true, admin);
		} catch (Exception e1) {
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
		} catch (IOException e) {
			System.err.println(e);
			JOptionPane.showMessageDialog(c,
				    "Could not connect to the automatic attendence system. Please try again later.",
				    "Error!",
				    JOptionPane.ERROR_MESSAGE);
			return FAILURE;
		} catch (Exception e) {
			System.err.println(e);
			return FAILURE;
		}
		
		if(ret.get(0).equals("OK")) {
			return SUCCESS;
		} else {
			JOptionPane.showMessageDialog(c,
				    "A system error occured. Sorry!",
				    "Error!",
				    JOptionPane.ERROR_MESSAGE);
			return FAILURE;
		}
	}
}
