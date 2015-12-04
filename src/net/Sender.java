package net;

import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.util.ArrayList;

import util.Constants;

public class Sender {
	public static URLConnection putData(ArrayList<String> keys, ArrayList<String> data) throws IOException,UnsupportedEncodingException,MalformedURLException, IllegalArgumentException {
		if(keys.size() != data.size()) {
			throw new IllegalArgumentException("The size of the keys and data do not match!");
		}
		String http = "";
		for(int i = 0; i < keys.size(); i++) {
			if(i == 0) {
				try {
					http += URLEncoder.encode(keys.get(i), "UTF-8") + "=" + URLEncoder.encode(data.get(i), "UTF-8");
				} catch (UnsupportedEncodingException e) {
					System.err.println("The encoder failed to create sendable data!");
					throw e;
				}
			} else {
				try {
					http += "&" + URLEncoder.encode(keys.get(i), "UTF-8") + "=" + URLEncoder.encode(data.get(i), "UTF-8");
				} catch (UnsupportedEncodingException e) {
					System.err.println("The encoder failed to create sendable data!");
					throw e;
				}
			}
		}
		System.out.println("SENT: " + http);
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
			System.err.println("IO Exception! Failed to write to server!");
			throw e;
		}
		return conn;
	}
}
