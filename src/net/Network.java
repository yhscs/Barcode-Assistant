package net;

import java.io.IOException;
import java.net.URLConnection;
import java.util.ArrayList;

public class Network {
	public static final int FAILURE = -1;
	public static final int SUCCESS = 0;
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
			conn = Sender.putData(keys,data);
			for(String ret : Getter.getData(conn)) {
				System.out.println(ret);
			}
		} catch (IOException e) {
			System.err.println(e);
			return FAILURE;
		} catch (Exception e) {
			System.err.println(e);
			return FAILURE;
		}
		return SUCCESS;
	}
}
