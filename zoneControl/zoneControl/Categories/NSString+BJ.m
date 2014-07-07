//
//  NSString+Rubiks.m
//  warlords
//
//  Created by Brandon Jue on 3/30/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "NSString+BJ.h"

@implementation NSString (BJ)

- (NSString*)stringWithoutExtension {
	NSString *retVal = self;
	
	NSRange rangeOfExtension = [self rangeOfString:@"."];
	if (rangeOfExtension.location!=NSNotFound) {
		retVal = [self substringToIndex:rangeOfExtension.location];
	}
	
	return retVal;
}
- (NSString*)trim {
	return [self stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
}
- (BOOL)containsRegex:(NSString*)pattern {
	NSRegularExpression *regex = [NSRegularExpression regularExpressionWithPattern:pattern options:NSRegularExpressionCaseInsensitive error:nil];
	int matches = (int)[regex numberOfMatchesInString:self options:0 range:NSMakeRange(0, self.length)];
	return matches > 0;
}
- (NSArray*)arrayOfMatchesRegex:(NSString*)pattern {
	NSRegularExpression *regex = [NSRegularExpression regularExpressionWithPattern:pattern options:NSRegularExpressionCaseInsensitive error:nil];
	NSArray *matches = [regex matchesInString:self options:0 range:NSMakeRange(0, self.length)];
	NSMutableArray *results = [NSMutableArray array];
	for (NSTextCheckingResult *match in matches) {
		NSMutableArray *result = [NSMutableArray array];
		for (int i=0; i<match.numberOfRanges; i++) {
			NSRange range = [match rangeAtIndex:i];
			if (range.length != NSNotFound) {
				NSString *text = [self substringWithRange:range];
				NSLog(@"%d: %@", i, text);
				[result addObject:text];
			}
		}
		[results addObject:result];
	}
	
	return results;
}

@end
