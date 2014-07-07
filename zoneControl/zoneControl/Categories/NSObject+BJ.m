//
//  NSObject+Rubiks.m
//  warlords
//
//  Created by Brandon Jue on 3/1/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "NSObject+BJ.h"

@implementation NSObject (BJ)

- (NSString*)KAObjectId {
	return [NSString stringWithFormat:@"%p",self];
}

@end
