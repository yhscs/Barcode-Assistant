package windows;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Component;
import java.awt.Container;
import java.awt.Dialog;
import java.awt.Dimension;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;
import javax.swing.border.TitledBorder;

import net.Network;
import util.Constants;
import util.Keyboard;

@SuppressWarnings("serial")
public class Setup extends JDialog{
	private final JTextField roomName = new JTextField(15);
	private final JPasswordField roomPassword = new JPasswordField(15);
	private final JPasswordField adminPassword = new JPasswordField(15);
	private final JButton createRoom = new JButton("Create Room");
	private final JButton help = new JButton("Help");
	private final JButton exit = new JButton("Exit");
	private final JCheckBox logStudentsOnly = new JCheckBox("Only log students");
	private final JCheckBox connectToDatabase = new JCheckBox("Connect to existing room");
	
	public Setup(JFrame frame) {
		super(frame, "First time setup...", Dialog.ModalityType.APPLICATION_MODAL);
		this.setPreferredSize(new Dimension(220,400));
		this.setSize(new Dimension(220,480));
        this.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        addComponentsToPane(this.getContentPane());
        this.pack();
        this.setResizable(false);
        this.setLocationRelativeTo(null);
        this.setVisible(true);
        this.addKeyListener(new Keyboard());
	}

	private void addComponentsToPane(Container p) {
		final JPanel panel = new JPanel();
		BoxLayout panelLayout = new BoxLayout(panel,BoxLayout.Y_AXIS);
		final JPanel loginArea = new JPanel();
		BoxLayout loginAreaLayout = new BoxLayout(loginArea,BoxLayout.Y_AXIS);
		panel.setLayout(panelLayout);
		loginArea.setLayout(loginAreaLayout);
		
		//region Variables used
		TitledBorder title;
		JPanel borderPanel;
		JLabel label;
		//endregion
		
		//region Typable field for the room name. Adds JTextField to fields array list.
		title = BorderFactory.createTitledBorder("Room name:");
		roomName.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		roomName.setName(Constants.ROOM_INDEX_KEY);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(roomName);
		loginArea.add(borderPanel);
		//endregion
		
		//region Typable field for the rooms password as a password field. Also added to JTextField array list
		title = BorderFactory.createTitledBorder("Room password:");
		roomPassword.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		roomPassword.setName(Constants.ROOM_PASSWORD_INDEX_KEY);
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
		
		//region Typable field for the Administrator's database password.
		title = BorderFactory.createTitledBorder("Administrator password:");
		adminPassword.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		adminPassword.setName(Constants.ADMIN_PASSWORD_INDEX_KEY);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(adminPassword);
		borderPanel.setMaximumSize(new Dimension(this.getWidth(), 60));
		borderPanel.setMinimumSize(new Dimension(this.getWidth(), 60));
		panel.add(borderPanel);
		//endregion
		
		//region Various other information

		logStudentsOnly.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(logStudentsOnly);
		label = new JLabel("(Disable student sign-outs)");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(label);
		//endregion
		
		//region Various other information
		connectToDatabase.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(connectToDatabase);
		label = new JLabel("(Use this program as another ");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(label);
		label = new JLabel("place to check in students)");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		connectToDatabase.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				if(connectToDatabase.isSelected()) {
					
				}
			}
		});
		panel.add(label);
		//endregion
		
		JPanel spacerPanel = new JPanel();
		panel.add(spacerPanel);
		
		createRoom.setAlignmentX(Component.CENTER_ALIGNMENT);
		createRoom.setMinimumSize(new Dimension(this.getWidth(), createRoom.getHeight()));
		createRoom.setMaximumSize(new Dimension(this.getWidth(), createRoom.getHeight()));
		createRoom.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent e) {
				System.out.println("The user is creating a database...");
				writeConfig();
			}
		});
		panel.add(createRoom);
		
		help.setAlignmentX(Component.CENTER_ALIGNMENT);
		help.setMinimumSize(new Dimension(this.getWidth(), help.getHeight()));
		help.setMaximumSize(new Dimension(this.getWidth(), help.getHeight()));
		help.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent e) {
				System.out.println("The user needs help.");
				showHelp();
			}
		});
		panel.add(help);
		
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
	
	private void writeConfig() {
		boolean allFieldsOK = getTyped(roomName) && getTyped(roomPassword) && getTyped(adminPassword);
		if(allFieldsOK) {
			System.out.println("Attempting to create the room " + roomName.getText() + ".");
			Network.createDatabase(this, roomName.getText(), roomPassword.getPassword(), adminPassword.getPassword());
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
	
	private void showHelp() {
		
	}
}
