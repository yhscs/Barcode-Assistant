package util;

import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;

import data.DataObject;
import net.Network;
import windows.Scanner;

public class Keyboard implements KeyListener {
	private String room;
	private String roomHash;
	private Scanner scanner;
	
	public Keyboard(Scanner s, String room, String roomHash) {
		this.room = room;
		this.roomHash = roomHash;
		this.scanner = s;
	}
	
	String que = "";
	
    /** Handle the key typed event from the text field. */
    public void keyTyped(KeyEvent e) {
        que += e.getKeyChar();
        if(que.length() > 1 && que.substring(que.length()-1, que.length()).equals("\n")) {
       		scanner.setHeaderText("Loading...");
        	scanner.setSubtitleText("Give me a second...");
    		scanner.setImage("/pictures/wait.png");	
    		scanner.setImage("/pictures/wait.png");	
        	que = que.substring(0,que.length() - 1);
        	System.out.print(que);
        	if(que.length() == 7) {
        		try {
        			Integer.parseInt(que);
            		System.out.println("... Accepted.");
            		DataObject data = new DataObject(que);
            		System.out.println(data);
            		if(Bell.getBell(data.getDate().getHour(), data.getDate().getMinute()).equals(Bell.BEFORE_SCHOOL) || 
            				Bell.getBell(data.getDate().getHour(), data.getDate().getMinute()).equals(Bell.AFTER_SCHOOL)) {
                		System.out.println("... There really isn't a purpose for a student to be checking in at this time.");
    	        		scanner.setImage("/pictures/no.png");	
    	           		scanner.setHeaderText("No need!");
    	            	scanner.setSubtitleText("It isn't during school hours right now. Try again some other time.");
            		} else {
	            		try {
	            			int status = Network.putData(room, roomHash, que, data.getDate().toString(), data.getDate().getHour(), data.getDate().getMinute());
	            			if(status == Network.CHECKIN) {
	            				scanner.setImage("/pictures/ok.png");	
	            				scanner.setHeaderText("Welcome!");
	            				scanner.setSubtitleText("Make sure you scan your Student ID again when you leave!");	
	            			} else if (status == Network.CHECKOUT) {
	            				scanner.setImage("/pictures/exit.png");	
	            				scanner.setHeaderText("Goodbye!");
	            				scanner.setSubtitleText("Thanks for signing out! See you soon!");	
	            			}
	            		} catch (Exception ohno) {
	            			System.out.println("Something happened!");
	        				scanner.setImage("/pictures/no.png");	
	            			scanner.setHeaderText("Oh no! Something broke :(");
	            			scanner.setSubtitleText(ohno.getMessage());
	            		}
            		}
        		} catch (NumberFormatException err) {
        			System.out.println("... Declined. Not real numbers.");
                	scanner.setHeaderText("Oh no! You tried to break something :(");
        			scanner.setSubtitleText("The thing you scanned had letters. A student ID does not contain letters.");
        			scanner.setImage("/pictures/no.png");	
        		}
        	} else {
        		System.out.println("... Declined. Not 7 characters.");
            	scanner.setHeaderText("Oh no! You tried to break something :(");
    			scanner.setSubtitleText("The thing you scanned was either too long or too short.");
    			scanner.setImage("/pictures/no.png");	
        	}
            que = "";
        }
    }

    /** Handle the key-pressed event from the text field. */
    public void keyPressed(KeyEvent e) {
    }

    /** Handle the key-released event from the text field. */
    public void keyReleased(KeyEvent e) {
    }
}

