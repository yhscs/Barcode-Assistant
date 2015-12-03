package net;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.util.ArrayList;

import util.Constants;

public class Sender {
	public static final int FAILURE_LOGIN = 1;
	public static final int FAILURE_IO = 2;
	public static final int FAILURE_INTERNAL = 3;
	public static final int FAILURE_ENCODE = 4;
	public static final int FAILURE_URL = 5;
	public static final int FAILURE_ARGS = 6;
	public static final int FAILURE_UNKNOWN = -1;
	public static final int SUCCESS = 0;
	
	public static URLConnection putData(ArrayList<String> keys, ArrayList<String> data) throws IOException,UnsupportedEncodingException,MalformedURLException, IllegalArgumentException {
		if(keys.size() != data.size()) {
			throw new IllegalArgumentException("The size of the keys and data do not match!");
		}
		String http = "";
		boolean firstKeyPair = true;
		for(String key : keys) {
			for(String datum : data) {
				if(firstKeyPair) {
					try {
						http += URLEncoder.encode(key, "UTF-8") + "=" + URLEncoder.encode(datum, "UTF-8");
					} catch (UnsupportedEncodingException e) {
						System.err.println("The encoder failed to create sendable data!");
						throw e;
					}
					firstKeyPair = false;
				} else {
					try {
						http += "&" + URLEncoder.encode(key, "UTF-8") + "=" + URLEncoder.encode(datum, "UTF-8");
					} catch (UnsupportedEncodingException e) {
						System.err.println("The encoder failed to create sendable data!");
						throw e;
					}
				}
			}
		}
	    URL url;
		try {
			url = new URL(Constants.DATABASE_PHP_URL);
		} catch (MalformedURLException e) {
			System.err.println("The internal URL is malformed. Tell a programmer to fix the internal URL in the constants class.");
			throw e;
		}
	    URLConnection conn;
	    OutputStreamWriter wr;
		try {
			conn = url.openConnection();
		    conn.setDoOutput(true);
		    wr = new OutputStreamWriter(conn.getOutputStream());
		    wr.write(http);
		    wr.flush();
		    wr.close();
		} catch (IOException e) {
			System.err.println("IO Exception! Failed to write!");
			throw e;
		}
		return conn;
	}
	
	public static int createDatabase(String room, String roomPassword, String adminPassword) {
		ArrayList<String> keys = new ArrayList<>();
		keys.add("ROOM");
		keys.add("ROOM_PASSWORD");
		keys.add("ADMIN_PASSWORD");
		
		ArrayList<String> data = new ArrayList<>();
		data.add(room);
		data.add(roomPassword);
		data.add(adminPassword);
	    URLConnection conn;
		try {
			conn = putData(keys,data);
		} catch (IllegalArgumentException e) {
			e.printStackTrace();
			return FAILURE_ARGS;
		} catch (UnsupportedEncodingException f) {
			return FAILURE_ENCODE;
		} catch (MalformedURLException g) {
			g.printStackTrace();
			return FAILURE_URL;
		} catch (IOException h) {
			h.printStackTrace();
			return FAILURE_IO;
		}
		BufferedReader rd;
		try {
			rd = new BufferedReader(new InputStreamReader(conn.getInputStream()));
		    String line;
		    while ((line = rd.readLine()) != null) {
		        System.out.println(line);
		    }
		    rd.close();
		} catch (IOException e) {
			e.printStackTrace();
			return FAILURE_IO;
		}
		return SUCCESS;
	}
}
