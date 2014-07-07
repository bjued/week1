//
//  NSArray+Rubiks.m
//  warlords
//
//  Created by Brandon Jue on 3/8/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "NSArray+BJ.h"

@implementation NSArray (BJ)

- (id)randomObject {
	if (self.count==0) return nil;
	
	int index = arc4random_uniform((int)self.count);
	return [self objectAtIndex:index];
}
- (float)summation {
	float total = 0;
	for (id obj in self) {
		total += [obj floatValue];
	}
	return total;
}
- (int)summationInt {
	float total = 0;
	for (id obj in self) {
		total += [obj intValue];
	}
	return total;
}
- (NSNumber*)closestToInt:(int)target {
	if (self.count == 0) return nil;
	NSNumber *winner = self[0];
	int diff = abs([winner intValue] - target);
	
	for (int i=1; i<self.count; i++) {
		int nextDiff = abs([self[i] intValue] - target);
		if (nextDiff < diff) {
			winner = self[i];
			diff = nextDiff;
		}
	}
	
	return winner;
}

@end
