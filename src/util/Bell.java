package util;

public class Bell {
	public static enum BellType {
		REGULAR,
		ELEVENAM,
		ONETHIRTYPM
	}
	
	private static class BellInside {
		String response;
		public boolean isBetween;
		private BellInside(String response, boolean isBetween) {
			this.response = response;
			this.isBetween = isBetween;
		}
	}
	public static String getBell(int hour, int minute) throws IndexOutOfBoundsException {
		if(isBetween(0,0,7,25,hour,minute)) {
			return "Before school";
		}
		BellInside b;
		if((b = isBetween(1,7,25,8,17,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(2,8,17,9,9,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(3,9,9,10,1,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(4,10,1,10,35,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(5,10,35,11,3,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(6,11,3,11,31,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(7,11,31,11,39,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(8,11,59,12,27,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(9,12,27,12,56,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(10,12,56,13,48,hour,minute)).isBetween) {
			return b.response;
		}
		if(isBetween(13,48,14,35,hour,minute)) {
			return "11";
		}
		return "After school";
	}
	
	public static boolean isBetween(int hour1, int minute1, int hour2, int minute2, int actualhour, int actualminute) {
		int hourandminute1 = hour1 * 60 + minute1;
		int hourandminute2 = hour2 * 60 + minute2;
		int actualhourandminute = actualhour * 60 + actualminute;
		if(actualhourandminute >= hourandminute1 && actualhourandminute < hourandminute2) {
			return true;
		}
		return false;
	}
	
	public static BellInside isBetween(int desiredPeriod, int hour1, int minute1, int hour2, int minute2, int actualhour, int actualminute) {
		int hourandminute1 = hour1 * 60 + minute1;
		int hourandminute2 = hour2 * 60 + minute2;
		int actualhourandminute = actualhour * 60 + actualminute;
		if(actualhourandminute >= hourandminute1 && actualhourandminute < hourandminute2) {
			if(actualhourandminute >= hourandminute2 - 5){
				return new BellInside(desiredPeriod + " (Passing Period)", true);
			} else {
				return new BellInside(desiredPeriod + "", true);
			}
		}
		return new BellInside(desiredPeriod + "", false);
	}
}
