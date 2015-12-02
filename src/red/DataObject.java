package red;

import java.time.LocalDateTime;

public class DataObject {
	private String id = "";
	private String name = "";
	private LocalDateTime info = LocalDateTime.now();
	private Date date = new Date(info.getYear(), info.getMonthValue(), info.getDayOfMonth(), info.getHour(), info.getMinute(), info.getSecond(), info.getNano());
	public DataObject(String id, String name) {
		this.id = id;
		this.name = name;
	}
	public String getId() {
		return id;
	}
	public String getName() {
		return name;
	}
	public Date getDate() {
		return date;
	}
	public String toString() {
		return id + ", " + name + ", " + date;
	}
}
