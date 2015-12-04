package net;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URLConnection;
import java.util.ArrayList;

import util.Constants;

public class Getter {
	public static ArrayList<String> getData(URLConnection conn) throws IOException{
		ArrayList<String> result = new ArrayList<>();
		BufferedReader rd;
		try {
			rd = new BufferedReader(new InputStreamReader(conn.getInputStream()));
		    String line;
		    while ((line = rd.readLine()) != null) {
		        result.add(line);
		    }
		    rd.close();
		} catch (IOException e) {
			System.err.println("IO Exception! Failed to read from server!");
			throw e;
		}
		for(String str : result) {
			System.out.println("GOT: " + str);
		}
		return result;
	}

	/**
	 * Gets a salt from the server
	 * @param true if the user is an admin, false otherwise.
	 * @param username Use Constants.INDEX_KEY_ROOM or Constants.INDEX_KEY_ADMIN for example.
	 * @return the salt from the database for that user.
	 * @throws IOException
	 * @throws Exception
	 */
	public static String getSaltFromDatabase(boolean admin, String username) throws IOException,Exception /*Descriptive...*/{
		ArrayList<String> keys = new ArrayList<>();
		keys.add(Constants.INDEX_KEY_REQUEST);
		if(admin) {
			keys.add(Constants.INDEX_KEY_ADMIN);
		} else {
			keys.add(Constants.INDEX_KEY_ROOM);
		}
		
		ArrayList<String> data = new ArrayList<>();
		data.add(Constants.REQUEST_SALT);
		data.add(username);
		
		URLConnection con;
		ArrayList<String> ret = new ArrayList<>();
		try {
			con = Sender.putData(keys,data);
			ret = Getter.getData(con);
		} catch (IOException e) {
			System.err.println("Could not connect to the automatic attendence system. Please try again later.");
			throw e;
		} catch (Exception e) {
			System.err.println(e);
			throw e;
		}
		
		if(ret.get(0).equals("OK")) {
			if(ret.get(1).equals(username)) {
				return ret.get(2);
			} else {
				throw new IOException("Database didn't like that.");
			}
		} else {
			throw new IOException("Database didn't like that.");
		}
	}
}
