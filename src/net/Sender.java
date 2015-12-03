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
			System.err.println("IO Exception! Failed to write to server!");
			throw e;
		}
		return conn;
	}
}
