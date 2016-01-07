package test;

import util.Bell;

public class UnitTester {

	public static void main(String[] args) {
		try {
			asserter(1, Bell.getBell(1,1).equals("Before school"));
			asserter(2, Bell.getBell(7,25).equals("1"));
			asserter(3, Bell.getBell(7,26).equals("1"));
			asserter(4, Bell.getBell(8,12).equals("1 (Passing Period)"));
			asserter(5, Bell.getBell(8,13).equals("1 (Passing Period)"));
			asserter(6, Bell.getBell(8,14).equals("1 (Passing Period)"));
			asserter(7, Bell.getBell(8,15).equals("1 (Passing Period)"));
			asserter(8, Bell.getBell(8,16).equals("1 (Passing Period)"));
			asserter(9, Bell.getBell(8,17).equals("2"));
			asserter(10, Bell.getBell(9,9).equals("3"));
			asserter(11, Bell.getBell(10,1).equals("4"));
			asserter(12, Bell.getBell(10,35).equals("5"));
			asserter(13, Bell.getBell(11,3).equals("6"));
			asserter(14, Bell.getBell(11,31).equals("7"));
			asserter(15, Bell.getBell(11,59).equals("8"));
			asserter(16, Bell.getBell(12,27).equals("9"));
			asserter(17, Bell.getBell(12,56).equals("10"));
			asserter(18, Bell.getBell(13,48).equals("11"));
			asserter(19, Bell.getBell(14,35).equals("After school"));
			asserter(20, Bell.getBell(9,3).equals("2"));
		} catch (AssertionError e) {
			System.out.println(e);
			System.out.println("Test failed!");
			System.exit(1);
		}
		System.out.println("Tests for a normal day done.");
	}
	
	public static void asserter(int context, boolean b) throws AssertionError {
		if(!b) {
			throw new AssertionError("Assertion failed at context " + context);
		}
	}

}
