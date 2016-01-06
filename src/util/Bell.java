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
		if((b = isBetween(1,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(2,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(3,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(4,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(5,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(6,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(7,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(8,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(9,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(10,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
		}
		if((b = isBetween(11,7,25,8,12,hour,minute)).isBetween) {
			return b.response;
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
			if(actualhourandminute <= hourandminute2 - 5){
				return new BellInside(desiredPeriod + " (Passing Period)", true);
			} else {
				return new BellInside(desiredPeriod + "", true);
			}
		}
		return new BellInside(desiredPeriod + "", false);
	}
}
