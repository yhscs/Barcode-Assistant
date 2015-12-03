package net;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URLConnection;
import java.util.ArrayList;

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
		return result;
	}

}
