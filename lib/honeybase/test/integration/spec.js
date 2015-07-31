honeybase.switchTestState();
honeybase.setTestType("normal/all");

describe("HoneyBase", function() {
  describe("Auth System", function() {
    describe("honeybase.signup", function() {
      it("should exist", function() {
        expect(typeof honeybase.signup).toEqual("function");
      });
    });
    describe("honeybase.signin", function() {
      it("should exist", function() {
        expect(typeof honeybase.signin).toEqual("function");
      });
    });
    describe("honeybase.logout", function() {
      it("should exist", function() {
        expect(typeof honeybase.logout).toEqual("function");
      });
    });
    describe("honeybase.auth", function() {
      it("should exist", function() {
        expect(typeof honeybase.auth).toEqual("function");
      });
    });
    describe("honeybase.current_user", function() {
      it("should exist", function() {
        expect(typeof honeybase.current_user).toEqual("function");
      });
    });
  });
  describe("Database System", function() {
    var db = honeybase.database("sample");
    describe("db.count", function() {
      it("should success", function(done) {
        db.count({}, function(flag, count){
          expect(typeof count).toEqual("number");
          done();
        });
      });
    });
    describe("db.first", function() {
      it("should success", function(done) {
        db.first(function(flag, firstId){
          expect(typeof firstId).toEqual("number");
          done();
        });
      });
    });
    describe("db.last", function() {
      it("should success", function(done) {
        db.last(function(flag, lastId){
          expect(typeof lastId).toEqual("number");
          done();
        });
      });
    });

    describe("db.insert", function() {
      it("should success", function(done) {
        db.insert({}, function(insertedFlag, insertedData){
          db.last(function(lastFlag, lastId){
            expect(insertedFlag).toBe(true);
            expect(lastFlag).toBe(true);
            expect(insertedData.id).toEqual(lastId);
            done();
          });
        });
      });
    });
    xdescribe("db.update", function() {
      it("should success", function(done) {
      });
    });
    xdescribe("db.select", function() {
      it("should success", function(done) {
        db.select({}).done(function(flag, data){
          // reffererが合わないから落ちた
          console.log(flag, data);
          pending();
          done();
        });
      });
    });
    xdescribe("db.delete", function() {
      it("should success", function(done) {
      });
    });
  });
});
