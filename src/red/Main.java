package red;

import windows.Scanner;
import windows.Setup;

public class Main {

	public static void main(String[] args) {
		Setup s = new Setup();
		while(!s.hasReturned()) {
			
		}
		new Scanner(s.getRoomName(),s.getHash());
	}

}
