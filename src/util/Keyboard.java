package util;

import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;
import java.util.ArrayList;

import data.DataObject;
import net.Network;
import windows.Scanner;

public class Keyboard implements KeyListener {
	private String room;
	private String roomHash;
	private Scanner scanner;
	
	private ArrayList<DataObject> checkedInIds = new ArrayList<>();
	
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
        	scanner.setHeaderText("Checking you in...");
			scanner.setSubtitleText("This shouldn't take more than a second...");
        	que = que.substring(0,que.length() - 1);
        	System.out.print(que);
        	if(que.length() == 7) {
        		try {
        			Integer.parseInt(que);
            		System.out.println("... Accepted.");
            		DataObject data = new DataObject(que);
            		System.out.println(data);
        			boolean isNotCheckedIn = true;
        			for(DataObject s : checkedInIds ) {
        				if(que.equals(s.getId())) {
        					isNotCheckedIn = false;
        					break;
        				}
        			}
            		try {
            			if(Network.putData(room, roomHash, que, data.getDate().toString(), data.getDate().getHour(), data.getDate().getMinute(), false, isNotCheckedIn) == Network.SUCCESS) {
            				if(isNotCheckedIn) {
            					checkedInIds.add(data);
                				scanner.setImage("/pictures/ok.png");	
                				scanner.setHeaderText("Welcome!");
                				scanner.setSubtitleText("Thanks for signing in!");	
            				} else {
            					for(int i = 0; i < checkedInIds.size(); i++) {
            						if(que.equals(checkedInIds.get(i).getId())) {
            							checkedInIds.remove(i);
                        				scanner.setImage("/pictures/exit.png");	
                        				scanner.setHeaderText("Goodbye!");
                        				scanner.setSubtitleText("Thanks for signing out!");	
            							break;
            						}
            					}
            				}
            			}
            		} catch (Exception ohno) {
        				if(isNotCheckedIn) {
        					checkedInIds.add(data);
        				} else {
        					for(int i = 0; i < checkedInIds.size(); i++) {
        						if(que.equals(checkedInIds.get(i).getId())) {
        							checkedInIds.remove(i);
        							break;
        						}
        					}
        				}
        				scanner.setImage("/pictures/no.png");	
            			scanner.setHeaderText("Oh no! You broke something :(");
            			scanner.setSubtitleText(ohno.getMessage());
            			ohno.printStackTrace();
            		}
        		} catch (NumberFormatException err) {
        			System.out.println("... Declined. Not real numbers.");
        		}
        	} else {
        		System.out.println("... Declined. Not 7 characters.");
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

