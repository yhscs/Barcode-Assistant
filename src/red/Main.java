package red;

import windows.Scanner;
import windows.Setup;

public class Main {

	public static void main(String[] args) {
		Setup s = new Setup();
		while(!s.hasReturned()) {
		    try {
		        Thread.sleep(50);
		    }catch (Exception e){
		        e.printStackTrace();
		    }
		}
		new Scanner(s.getRoomName(),s.getHash());
	}

}
