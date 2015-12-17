package data;

import java.time.LocalDateTime;

public class DataObject {
	private String id = "";
	private LocalDateTime info = LocalDateTime.now();
	private Date date = new Date(info.getYear(), info.getMonthValue(), info.getDayOfMonth(), info.getHour(), info.getMinute(), info.getSecond(), info.getNano());
	public DataObject(String id) {
		this.id = id;
	}
	public String getId() {
		return id;
	}
	public Date getDate() {
		return date;
	}
	public String toString() {
		return id + ", " + date;
	}
}
