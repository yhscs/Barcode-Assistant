package windows;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Component;
import java.awt.Container;
import java.awt.Dialog;
import java.awt.Dimension;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.WindowEvent;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;
import javax.swing.border.TitledBorder;

import net.Network;
import util.HashAndReturn;
import util.keys.Indexes;

@SuppressWarnings("serial")
public class Setup extends JDialog{
	private final JTextField roomName = new JTextField(20);
	private final JPasswordField roomPassword = new JPasswordField(20);
	private final JTextField adminName = new JTextField(20);
	private final JPasswordField adminPassword = new JPasswordField(20);
	private final String createRoomAlt = "Connect to Existing Room";
	private final String createRoomDef = "Create Room";
	private final JButton createRoom = new JButton(createRoomDef);
	private final JButton exit = new JButton("Exit");
	private final JCheckBox connectToDatabase = new JCheckBox("Connect to existing room");
	
	private String roomNameString;
	private String roomHash;
	private boolean hasReturned = false;
	
	public Setup() {
		super(null, "Log in", Dialog.ModalityType.APPLICATION_MODAL);
		this.setPreferredSize(new Dimension(260,460));
		this.setSize(new Dimension(260,460));
        this.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        addComponentsToPane(this.getContentPane());
        this.pack();
        this.setResizable(false);
        this.setLocationRelativeTo(null);
        this.setVisible(true);
	}

	private void addComponentsToPane(Container p) {
		final JPanel panel = new JPanel();
		BoxLayout panelLayout = new BoxLayout(panel,BoxLayout.Y_AXIS);
		final JPanel loginArea = new JPanel();
		BoxLayout loginAreaLayout = new BoxLayout(loginArea,BoxLayout.Y_AXIS);
		final JPanel adminArea = new JPanel();
		BoxLayout adminAreaLayout = new BoxLayout(adminArea,BoxLayout.Y_AXIS);
		panel.setLayout(panelLayout);
		loginArea.setLayout(loginAreaLayout);
		adminArea.setLayout(adminAreaLayout);
		
		//region Variables used
		TitledBorder title;
		JPanel borderPanel;
		JLabel label;
		//endregion
		
		//region Typable field for the room name. Adds JTextField to fields array list.
		title = BorderFactory.createTitledBorder("Room name:");
		roomName.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		roomName.setName(Indexes.ROOM);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(roomName);
		loginArea.add(borderPanel);
		//endregion
		
		//region Typable field for the rooms password as a password field. Also added to JTextField array list
		title = BorderFactory.createTitledBorder("Room password:");
		roomPassword.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		roomPassword.setName(Indexes.ROOM_PASSWORD);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(roomPassword);
		loginArea.add(borderPanel);
		//endregion
		
		//region Creates a border around the login area and sets min/max height
		title = BorderFactory.createTitledBorder("Your room information:");
		loginArea.setBorder(title);
		loginArea.setMaximumSize(new Dimension(this.getWidth(), 140));
		loginArea.setMinimumSize(new Dimension(this.getWidth(), 140));
		panel.add(loginArea);
		//endregion
		
		//region Connect to existing database region.
		connectToDatabase.setAlignmentX(Component.CENTER_ALIGNMENT);
		connectToDatabase.setOpaque(false);
		connectToDatabase.setSelected(true);
		toggleConnectToDatabase();
		panel.add(connectToDatabase);
		label = new JLabel("(Log in to an existing room. This");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(label);
		label = new JLabel("can also be used to set up");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(label);
		label = new JLabel("another checkin area.)");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(label);
		connectToDatabase.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				toggleConnectToDatabase();
			}
		});
		//endregion
		
		//region Typable field for the Administrator's database user name.
		title = BorderFactory.createTitledBorder("Username:");
		adminName.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		adminName.setName(Indexes.ADMIN);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(adminName);
		adminArea.add(borderPanel);
		//endregion
		
		//region Typable password field for the Administrator's database password.
		title = BorderFactory.createTitledBorder("Password:");
		adminPassword.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		adminPassword.setName(Indexes.ADMIN_PASSWORD);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(adminPassword);
		adminArea.add(borderPanel);
		//endregion
		
		//region Creates a border around the login area and sets min/max height
		title = BorderFactory.createTitledBorder("Administrator information:");
		adminArea.setBorder(title);
		adminArea.setMaximumSize(new Dimension(this.getWidth(), 140));
		adminArea.setMinimumSize(new Dimension(this.getWidth(), 140));
		panel.add(adminArea);
		//endregion
		
		JPanel spacerPanel = new JPanel();
		panel.add(spacerPanel);
		
		createRoom.setAlignmentX(Component.CENTER_ALIGNMENT);
		createRoom.setMinimumSize(new Dimension(this.getWidth(), createRoom.getHeight()));
		createRoom.setMaximumSize(new Dimension(this.getWidth(), createRoom.getHeight()));
		createRoom.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent e) {
				System.out.println("The user is creating a database...");
				accept();
			}
		});
		panel.add(createRoom);
		
		exit.setAlignmentX(Component.CENTER_ALIGNMENT);
		exit.setMinimumSize(new Dimension(this.getWidth(), exit.getHeight()));
		exit.setMaximumSize(new Dimension(this.getWidth(), exit.getHeight()));
		exit.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent e) {
				System.out.println("The user quit the setup.");
				System.exit(0);
			}
		});
		panel.add(exit);


		p.add(panel, BorderLayout.CENTER);
	}
	
	private void accept() {
		if(!connectToDatabase.isSelected()) {
			boolean field1 = getTyped(roomName);
			boolean field2 = getTyped(roomPassword);
			boolean field3 = getTyped(adminName);
			boolean field4 = getTyped(adminPassword);
			boolean allFieldsOK = field1 && field2 && field3 && field4;
			if(allFieldsOK) {
				System.out.println("Attempting to create the room " + roomName.getText() + ".");
				if(Network.createDatabase(this, roomName.getText(), roomPassword.getPassword(), adminName.getText(), adminPassword.getPassword()) == Network.SUCCESS) {
					JOptionPane.showMessageDialog(this,
						    "Created the room succesfully! You can now use the new account to log into the attendance system.",
						    "Success!",
						    JOptionPane.INFORMATION_MESSAGE);
					adminName.setText("");
					adminPassword.setText("");
					connectToDatabase.setSelected(true);
					toggleConnectToDatabase();
				}
			}
		} else {
			boolean field1 = getTyped(roomName);
			boolean field2 = getTyped(roomPassword);
			boolean allFieldsOK = field1 && field2;
			if(allFieldsOK) {
				System.out.println("Attempting to connect to existing room " + roomName.getText() + ".");
				HashAndReturn result = Network.testLogin(this, roomName.getText(), roomPassword.getPassword());
				if(result.getResult() == Network.SUCCESS) {
					this.roomNameString = roomName.getText();
					this.roomHash = result.getHash();
					this.hasReturned = true;
					this.setDefaultCloseOperation(DISPOSE_ON_CLOSE);
					this.dispatchEvent(new WindowEvent(this, WindowEvent.WINDOW_CLOSING));
				}
			}
		}
	}
	
	private boolean getTyped(JTextField j) {
		if(j.getText().equals("")) {
			j.setBorder(BorderFactory.createLineBorder(new Color(255,0,0)));
			System.out.println("The user did not type anything in the " + j.getName().toLowerCase().replace("_", " ") + " field!");
			return false;
		} else {
			j.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
			return true;
		}
	}
	
	private void toggleConnectToDatabase() {
		if(connectToDatabase.isSelected()) {
			adminPassword.setEnabled(false);
			adminPassword.setBackground(Color.GRAY);
			adminPassword.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
			adminName.setEnabled(false);
			adminName.setBackground(Color.GRAY);
			adminName.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
			roomPassword.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
			roomName.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
			createRoom.setText(createRoomAlt);
		} else {
			adminPassword.setEnabled(true);
			adminPassword.setBackground(Color.WHITE);
			adminName.setEnabled(true);
			adminName.setBackground(Color.WHITE);
			createRoom.setText(createRoomDef);
		}
	}

	public String getRoomName() {
		return roomNameString;
	}
	
	public String getHash() { 
		return roomHash;
	}

	public boolean hasReturned() {
		return hasReturned;
	}
}
