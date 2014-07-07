//
//  NSString+BK.m
//  BlacK
//
//  Created by Brandon Jue on 5/31/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "NSString+BK.h"

#define kBKVariableRegex @"(\\$[a-zA-Z_][a-zA-Z0-9_]*)"

@implementation NSString (BK)

- (BOOL)containsVariable {
	return [self containsRegex:kBKVariableRegex];
}
- (BOOL)isVariable {
	NSString *trimmed = [self trim];
	return [trimmed containsRegex:@"^(\\$[a-zA-Z_][a-zA-Z0-9_]*)$"];
}
- (NSArray*)variables {
	return [self arrayOfMatchesRegex:kBKVariableRegex];
}

@end
