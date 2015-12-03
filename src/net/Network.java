package net;

import java.awt.Component;
import java.io.IOException;
import java.net.URLConnection;
import java.util.ArrayList;
import java.util.Arrays;

import javax.swing.JOptionPane;

import util.Constants;

public class Network {
	public static final int FAILURE = -1;
	public static final int SUCCESS = 0;
	public static int createDatabase(Component c, String room, char[] roomPassword, char[] adminPassword) {
		ArrayList<String> keys = new ArrayList<>();
		keys.add(Constants.ROOM_INDEX_KEY);
		keys.add(Constants.ROOM_PASSWORD_INDEX_KEY);
		keys.add(Constants.ADMIN_PASSWORD_INDEX_KEY);
		
		ArrayList<String> data = new ArrayList<>();
		String hash;
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
		
		hash = Constants.getSHA512Hash(Constants.toBytes(roomPassword));
		data.add(hash);
		Arrays.fill(roomPassword, '\u0000'); // clear sensitive data
		System.out.println(hash);
		
		hash = Constants.getSHA512Hash(Constants.toBytes(adminPassword));
		data.add(hash);
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
		} else if(ret.get(0).equals("BAD " + Constants.ADMIN_PASSWORD_INDEX_KEY)){
			JOptionPane.showMessageDialog(c,
				    "The administrator password is incorrect. Contact the person who runs the attendance system.",
				    "Bad password!",
				    JOptionPane.WARNING_MESSAGE);
			return FAILURE;
		} else {
			JOptionPane.showMessageDialog(c,
				    "A system error occured. Sorry!",
				    "Error!",
				    JOptionPane.ERROR_MESSAGE);
			return FAILURE;
		}
	}
}
