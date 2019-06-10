
.PHONY: proto
proto:
	protoc --php_out=./protoClasses/ ./protos/*.proto

