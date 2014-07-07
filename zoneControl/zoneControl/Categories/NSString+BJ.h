//
//  NSString+Rubiks.h
//  warlords
//
//  Created by Brandon Jue on 3/30/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSString (BJ)

- (NSString*)stringWithoutExtension;
- (NSString*)trim;
- (BOOL)containsRegex:(NSString*)pattern;
- (NSArray*)arrayOfMatchesRegex:(NSString*)pattern;

@end
