package windows;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Component;
import java.awt.Container;
import java.awt.Dialog;
import java.awt.Dimension;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.ArrayList;

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
	
	private ArrayList<JTextField> fields = new ArrayList<>();
	
	public Setup(JFrame frame) {
		super(frame, "First time setup...", Dialog.ModalityType.APPLICATION_MODAL);
		this.setPreferredSize(new Dimension(220,360));
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
		JTextField text;
		JButton jb;
		JCheckBox check;
		JLabel label;
		//endregion
		
		//region Typable field for the room name. Adds JTextField to fields array list.
		title = BorderFactory.createTitledBorder("Room name:");
		text = new JTextField(15);
		text.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		text.setName(Constants.ROOM_INDEX_KEY);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(text);
		loginArea.add(borderPanel);
		fields.add(text);
		//endregion
		
		//region Typable field for the rooms password as a password field. Also added to JTextField array list
		title = BorderFactory.createTitledBorder("Room password:");
		text = new JPasswordField(15);
		text.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		text.setName(Constants.ROOM_PASSWORD_INDEX_KEY);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(text);
		loginArea.add(borderPanel);
		fields.add(text);
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
		text = new JPasswordField(15);
		text.setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
		text.setName(Constants.ADMIN_PASSWORD_INDEX_KEY);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(text);
		borderPanel.setMaximumSize(new Dimension(this.getWidth(), 60));
		borderPanel.setMinimumSize(new Dimension(this.getWidth(), 60));
		panel.add(borderPanel);
		fields.add(text);
		//endregion
		
		//region Various other information
		check = new JCheckBox("Only log students");
		check.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(check);
		label = new JLabel("(Disable student");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(label);
		label = new JLabel("sign-outs)");
		label.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(label);
		//endregion
		
		JPanel spacerPanel = new JPanel();
		panel.add(spacerPanel);
		
		jb = new JButton("Create Room");
		jb.setAlignmentX(Component.CENTER_ALIGNMENT);
		jb.setMinimumSize(new Dimension(this.getWidth(), jb.getHeight()));
		jb.setMaximumSize(new Dimension(this.getWidth(), jb.getHeight()));
		panel.add(jb);
		jb.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent e) {
				System.out.println("The user is creating a database...");
				writeConfig();
			}
		});
		jb = new JButton("Exit");
		jb.setAlignmentX(Component.CENTER_ALIGNMENT);
		jb.setMinimumSize(new Dimension(this.getWidth(), jb.getHeight()));
		jb.setMaximumSize(new Dimension(this.getWidth(), jb.getHeight()));
		panel.add(jb);
		jb.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent e) {
				System.out.println("The user quit the setup.");
				System.exit(0);
			}
		});
		panel.add(jb);
		jb = new JButton("Help");
		jb.setAlignmentX(Component.CENTER_ALIGNMENT);
		jb.setMinimumSize(new Dimension(this.getWidth(), jb.getHeight()));
		jb.setMaximumSize(new Dimension(this.getWidth(), jb.getHeight()));
		jb.addActionListener(new ActionListener(){
			public void actionPerformed(ActionEvent e) {
				System.out.println("The user needs help.");
				showHelp();
			}
		});
		panel.add(jb);

		p.add(panel, BorderLayout.CENTER);
	}
	
	private void writeConfig() {
		boolean allFieldsOK = true;
		for(int i = 0; i < fields.size(); i++) {
			if(fields.get(i).getText().equals("")) {
				fields.get(i).setBorder(BorderFactory.createLineBorder(new Color(255,0,0)));
				System.out.println("The user did not type anything in the " + fields.get(i).getName().toLowerCase().replace("_", " ") + " field!");
				allFieldsOK = false;
			} else {
				fields.get(i).setBorder(BorderFactory.createLineBorder(new Color(122,138,153)));
			}
		}
		if(allFieldsOK) {
			String room = "";
			for (JTextField j : fields) {
				if(j.getName().equals(Constants.ROOM_INDEX_KEY)) {
					room = j.getText();
				}
			}
			String roomPassword = "";
			for (JTextField j : fields) {
				if(j.getName().equals(Constants.ROOM_PASSWORD_INDEX_KEY)) {
					room = j.getText();
				}
			}
			String adminPassword = "";
			for (JTextField j : fields) {
				if(j.getName().equals(Constants.ADMIN_PASSWORD_INDEX_KEY)) {
					room = j.getText();
				}
			}
			System.out.println("Attempting to create the room " + room + ".");
			Network.createDatabase(room, roomPassword, adminPassword);
		}
	}
	
	private void showHelp() {
		
	}
}
